<?php
session_start();
require_once 'db.php';

// Access control: Only editors
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'editor') {
    http_response_code(403);
    echo "<h1>Access denied</h1>";
    exit();
}

// Handle enable/disable action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id'], $_POST['action'])) {
    $article_id = intval($_POST['article_id']);
    $enable = ($_POST['action'] === 'enable') ? 1 : 0;

    $check_stmt = $conn->prepare("SELECT 1 FROM comment_settings WHERE article_id = ?");
    $check_stmt->bind_param("i", $article_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE comment_settings SET comments_enabled = ? WHERE article_id = ?");
        $stmt->bind_param("ii", $enable, $article_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO comment_settings (article_id, comments_enabled) VALUES (?, ?)");
        $stmt->bind_param("ii", $article_id, $enable);
    }

    $stmt->execute();
    $stmt->close();
    header("Location: comment_settings.php");
    exit();
}

// Fetch articles with comment settings and author
$query = "
    SELECT a.article_id, a.title, u.username, COALESCE(cs.comments_enabled, 1) AS comments_enabled
    FROM articles a
    JOIN users u ON a.author_id = u.user_id
    LEFT JOIN comment_settings cs ON a.article_id = cs.article_id
    ORDER BY a.created_at DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Comment Settings | Austro-Asian Times</title>
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

        table {
            background-color: var(--card-bg);
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
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
            <li><a href="manage_comment.php"><i class="fas fa-comments me-2"></i> Manage Comments</a></li>
            <li class="active"><a href="comment_settings.php"><i class="fas fa-comment me-2"></i> Comment Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="main-content">
        <h3 class="mb-4">Comment Settings</h3>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Article Title</th>
                    <th>Author</th>
                    <th>Comments</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['title']) ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= $row['comments_enabled'] ? 'Enabled' : 'Disabled' ?></td>
                        <td>
                            <form method="post" class="d-inline">
                                <input type="hidden" name="article_id" value="<?= $row['article_id'] ?>">
                                <?php if ($row['comments_enabled']): ?>
                                    <button type="submit" name="action" value="disable" class="btn btn-danger btn-sm">Disable</button>
                                <?php else: ?>
                                    <button type="submit" name="action" value="enable" class="btn btn-success btn-sm">Enable</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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