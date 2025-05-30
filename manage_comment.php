<?php
session_start();
require_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    http_response_code(403);
    echo "<h1>Access Denied</h1>";
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Capture referrer only on initial GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
}

// Process comment status update if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'], $_POST['new_status'])) {
    $comment_id = intval($_POST['comment_id']);
    $new_status = $_POST['new_status'] === 'approved' ? 'approved' : 'pending';

    // Check permission
    $query = "SELECT a.author_id 
              FROM comments c 
              JOIN articles a ON c.article_id = a.article_id 
              WHERE c.comment_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $comment_id);
    $stmt->execute();
    $stmt->bind_result($author_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_role === 'editor' || ($user_role === 'journalist' && $author_id == $user_id)) {
        $update = $conn->prepare("UPDATE comments SET status = ? WHERE comment_id = ?");
        $update->bind_param("si", $new_status, $comment_id);
        $update->execute();
        $update->close();
    }

    // Redirect to avoid form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch comments based on role
if ($user_role === 'editor') {
    $sql = "SELECT c.comment_id, c.name, c.content, c.status, c.created_at, a.title 
            FROM comments c 
            JOIN articles a ON c.article_id = a.article_id 
            ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT c.comment_id, c.name, c.content, c.status, c.created_at, a.title 
            FROM comments c 
            JOIN articles a ON c.article_id = a.article_id 
            WHERE a.author_id = ? 
            ORDER BY c.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Comments | Austro-Asian Times</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #6fb1fc;
            --secondary: #c0e0f7;
            --background: #f8f9fa;
            --card-bg: #ffffff;
            --text-dark: #343a40;
            --text-muted: #6c757d;
            --sidebar-bg: #e9f1fb;
            --brand-color: #5089c6;
        }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            margin: 0;
        }

        .navbar {
            background: var(--sidebar-bg);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 0.6rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1100;
        }

        .navbar-brand {
            margin-left: 10px;
            font-weight: bold;
            color: var(--brand-color);
            font-size: 1.2rem;
            display: flex;
            align-items: flex-end;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: -250px;
            width: 250px;
            background-color: var(--sidebar-bg);
            transition: left 0.3s ease;
            z-index: 1000;
        }

        .sidebar.show {
            left: 0;
        }

        .sidebar-header {
            padding: 20px;
            background: var(--primary);
            color: white;
            text-align: center;
        }

        .user-profile {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: #f1f5fb;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-menu li {
            padding: 12px 20px;
            border-bottom: 1px solid #dee2e6;
        }

        .sidebar-menu li a {
            text-decoration: none;
            color: var(--text-dark);
            display: block;
        }

        .sidebar-menu li.active,
        .sidebar-menu li:hover {
            background-color: #dbeafe;
        }

        .overlay {
            display: none;
            position: fixed;
            background-color: rgba(0, 0, 0, 0.4);
            top: 0;
            left: 0;
            height: 100%;
            width: 100%;
            z-index: 900;
        }

        .overlay.show {
            display: block;
        }

        .main-content {
            margin-left: 0;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        @media (min-width: 768px) {
            .sidebar {
                left: 0;
            }

            .main-content {
                margin-left: 250px;
            }

            .overlay {
                display: none !important;
            }
        }

        .comment-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            background-color: var(--card-bg);
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        }

        .comment-meta {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .status-approved {
            color: green;
        }

        .status-pending {
            color: orange;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <button class="navbar-toggler" id="toggleSidebar"><i class="fas fa-bars"></i></button>
        <div class="navbar-brand">Austro-Asian Times</div>
    </nav>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h5>Editor Dashboard</h5>
        </div>
        <div class="user-profile">
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['username']) ?>&background=random" alt="User">
            <div>
                <div><?= htmlspecialchars($_SESSION['username']) ?></div>
                <small class="text-muted"><?= ucfirst($_SESSION['role']) ?></small>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="editor_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a href="articles.php"><i class="fas fa-newspaper me-2"></i> Articles</a></li>
            <li><a href="pending.php"><i class="fas fa-clock me-2"></i> Pending Approval</a></li>
            <li><a href="journalists.php"><i class="fas fa-users me-2"></i> Journalists</a></li>
            <li><a href="add_journalist.php"><i class="fas fa-user-plus me-2"></i> Add Journalist</a></li>
            <li class="active"><a href="manage_comment.php"><i class="fas fa-comments me-2"></i> Manage Comments</a></li>
            <li><a href="comment_settings.php"><i class="fas fa-comment me-2"></i> Comment Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="main-content">
        <h3 class="mb-4">Manage Comments</h3>

        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="comment-box">
                <div class="comment-meta">
                    <strong><?php echo htmlspecialchars($row['name']); ?></strong> |
                    On article: <em><?php echo htmlspecialchars($row['title']); ?></em><br>
                    Posted: <?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?> |
                    Status: <span class="status-<?php echo $row['status']; ?>"><?php echo ucfirst($row['status']); ?></span>
                </div>
                <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>

                <form method="POST" class="d-inline">
                    <input type="hidden" name="comment_id" value="<?php echo $row['comment_id']; ?>">
                    <input type="hidden" name="new_status" value="<?php echo $row['status'] === 'approved' ? 'pending' : 'approved'; ?>">
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        Set as <?php echo $row['status'] === 'approved' ? 'Pending' : 'Approved'; ?>
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>

    <script>
        const sidebar = document.getElementById("sidebar");
        const overlay = document.getElementById("overlay");
        const toggleBtn = document.getElementById("toggleSidebar");

        toggleBtn.addEventListener("click", () => {
            sidebar.classList.toggle("show");
            overlay.classList.toggle("show");
        });

        overlay.addEventListener("click", () => {
            sidebar.classList.remove("show");
            overlay.classList.remove("show");
        });
    </script>
</body>

</html>