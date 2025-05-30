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
$username = htmlspecialchars($_SESSION['username'] ?? '');

// Capture referrer only on initial GET request
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_SERVER['HTTP_REFERER'])) {
    $_SESSION['previous_page'] = $_SERVER['HTTP_REFERER'];
}

// Process comment status update if POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_id'], $_POST['new_status'])) {
    $comment_id = intval($_POST['comment_id']);
    $new_status = $_POST['new_status'] === 'approved' ? 'approved' : 'pending';

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
    <title>Manage Comments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            overflow-x: hidden;
        }

        .top-navbar {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1040;
        }

        .hamburger {
            font-size: 24px;
            background: none;
            border: none;
            color: #333;
        }

        .sidebar {
            background-color: #1e1e2f;
            color: white;
            height: 100vh;
            width: 240px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            padding-top: 60px;
            transition: transform 0.3s ease;
            transform: translateX(0);
        }

        .sidebar-hidden {
            transform: translateX(-100%);
        }

        .sidebar a {
            color: #ddd;
            display: block;
            padding: 12px 20px;
            text-decoration: none;
        }

        .sidebar a:hover,
        .sidebar .active a {
            background-color: #343a40;
            color: #fff;
        }

        .main-content {
            margin-left: 240px;
            padding: 40px 20px;
            transition: margin-left 0.3s ease;
        }

        .content-expanded {
            margin-left: 0 !important;
        }

        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0;
            }
        }

        .comment-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            background-color: #fefefe;
        }

        .comment-meta {
            font-size: 0.85rem;
            color: #666;
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

    <!-- Top Navbar -->
    <div class="top-navbar">
        <button class="hamburger" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="m-0">Manage Comments</h4>
        <span class="text-muted d-none d-md-inline"><?php echo $username; ?></span>
    </div>

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <h4 class="text-center text-light mt-3">Journalist Panel</h4>
        <ul class="list-unstyled mt-4">
            <li><a href="journalist_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="add_article.php"><i class="fas fa-plus me-2"></i> Add Article</a></li>
            <li><a href="view_articles.php"><i class="fas fa-newspaper me-2"></i> View All Articles</a></li>
            <li><a href="edit_profile.php"><i class="fas fa-user-edit me-2"></i> Edit Profile</a></li>
            <li class="active"><a href="j.manage_comment.php"><i class="fas fa-comments me-2"></i> Manage Comment</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main id="mainContent" class="main-content">

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
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('mainContent');
            sidebar.classList.toggle('sidebar-hidden');
            content.classList.toggle('content-expanded');
        }
    </script>

</body>

</html>