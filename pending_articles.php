<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'editor') {
    header("Location: login.php");
    exit();
}

require 'db.php';

if (isset($_GET['action'], $_GET['article_id'])) {
    $action = $_GET['action'];
    $article_id = (int) $_GET['article_id'];

    if ($action === 'change_status' && isset($_GET['status'])) {
        $new_status = $_GET['status'];
        $allowed_statuses = ['draft', 'pending', 'approved', 'declined'];

        if (in_array($new_status, $allowed_statuses)) {
            $stmt = $conn->prepare("UPDATE articles SET status = ? WHERE article_id = ?");
            $stmt->bind_param('si', $new_status, $article_id);
            $stmt->execute();
        }
    } elseif ($action === 'delete') {
        $conn->begin_transaction();
        try {
            $stmt1 = $conn->prepare("DELETE FROM article_tags WHERE article_id = ?");
            $stmt1->bind_param('i', $article_id);
            $stmt1->execute();

            $stmt2 = $conn->prepare("DELETE FROM articles WHERE article_id = ?");
            $stmt2->bind_param('i', $article_id);
            $stmt2->execute();

            $conn->commit();
        } catch (Exception $e) {
            $conn->rollback();
        }
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Editor Articles - Austro-Asian Times</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #5089c6;
            --sidebar-bg: #f8f9fa;
            --text-dark: #2c3e50;
            --brand-color: #5089c6;
            --background: #f0f4f8;
        }

        body {
            background-color: var(--background);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
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

        .badge-pending {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-approved {
            background-color: #28a745;
            color: #fff;
        }

        .badge-declined {
            background-color: #dc3545;
            color: #fff;
        }

        .table-responsive .table {
            background-color: #e8eef7;
            color: #2c3e50;
        }

        .table thead {
            background-color: #d9e5f6;
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
                <h6 class="mb-0"><?= htmlspecialchars($_SESSION['username']) ?></h6>
                <small class="text-muted">Editor</small>
            </div>
        </div>
        <ul class="sidebar-menu">
            <li><a href="editor_dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li class="active"><a href="articles.php"><i class="fas fa-newspaper me-2"></i> Articles</a></li>
            <li><a href="pending_articles.php"><i class="fas fa-clock me-2"></i> Pending Approval</a></li>
            <li><a href="journalists.php"><i class="fas fa-users me-2"></i> Journalists</a></li>
            <li><a href="add_journalist.php"><i class="fas fa-user-plus me-2"></i> Add Journalist</a></li>
            <li><a href="manage_comment.php"><i class="fas fa-comments me-2"></i> Manage Comments</a></li>
            <li><a href="comment_settings.php"><i class="fas fa-comment me-2"></i> Comment Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="main-content">
        <h2 class="mb-3">All Articles</h2>

        <input type="text" id="articleSearch" class="form-control mb-3" placeholder="Search by title or author...">

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $conn->prepare("
                    SELECT a.article_id, a.title, a.created_at, a.status, u.username 
                    FROM articles a 
                    LEFT JOIN users u ON a.author_id = u.user_id 
                    WHERE a.status NOT IN ('approved', 'declined')
                    ORDER BY a.created_at DESC
                ");
                    $stmt->execute();
                    $result = $stmt->get_result();

                    $serial = 1;
                    while ($row = $result->fetch_assoc()):
                        $badgeClass = match ($row['status']) {
                            'pending' => 'badge-pending',
                            'approved' => 'badge-approved',
                            'declined' => 'badge-declined',
                            default => 'bg-secondary'
                        };
                    ?>
                        <tr>
                            <td><?= $serial++ ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['username'] ?? 'Unknown') ?></td>
                            <td>
                                <span class="badge <?= $badgeClass ?> status-badge" data-id="<?= $row['article_id'] ?>">
                                    <?= ucfirst($row['status']) ?>
                                </span>
                            </td>
                            <td><?= $row['created_at'] ?></td>
                            <td>
                                <a href="view_article.php?id=<?= $row['article_id'] ?>" class="btn btn-sm btn-info">View</a>

                                <div class="btn-group me-1">
                                    <button type="button" class="btn btn-sm btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        Change Status
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <?php
                                        foreach (['draft', 'pending', 'approved', 'declined'] as $statusOption):
                                            if ($statusOption !== $row['status']):
                                        ?>
                                                <li>
                                                    <a class="dropdown-item change-status"
                                                        href="#"
                                                        data-id="<?= $row['article_id'] ?>"
                                                        data-status="<?= $statusOption ?>">
                                                        <?= ucfirst($statusOption) ?>
                                                    </a>
                                                </li>
                                        <?php endif;
                                        endforeach; ?>
                                    </ul>
                                </div>

                                <a href="#" class="btn btn-sm btn-danger delete-article" data-id="<?= $row['article_id'] ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('toggleSidebar').addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('show');
            document.getElementById('overlay').classList.toggle('show');
        });

        document.getElementById('overlay').addEventListener('click', () => {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('overlay').classList.remove('show');
        });

        $('#articleSearch').on('keyup', function() {
            const query = $(this).val().toLowerCase();
            $('table tbody tr').each(function() {
                const title = $(this).find('td:nth-child(2)').text().toLowerCase();
                const author = $(this).find('td:nth-child(3)').text().toLowerCase();
                $(this).toggle(title.includes(query) || author.includes(query));
            });
        });

        $('.change-status').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const status = $(this).data('status');

            $.get('articles.php', {
                action: 'change_status',
                article_id: id,
                status: status
            }, function() {
                const badge = $(`.status-badge[data-id="${id}"]`);
                badge.text(status.charAt(0).toUpperCase() + status.slice(1));
                badge.removeClass('badge-pending badge-approved badge-declined bg-secondary');

                switch (status) {
                    case 'pending':
                        badge.addClass('badge-pending');
                        break;
                    case 'approved':
                        badge.addClass('badge-approved');
                        break;
                    case 'declined':
                        badge.addClass('badge-declined');
                        break;
                    default:
                        badge.addClass('bg-secondary');
                }
            });
        });

        $('.delete-article').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this article?')) {
                $.get('articles.php', {
                    action: 'delete',
                    article_id: id
                }, function() {
                    location.reload();
                });
            }
        });
    </script>
</body>

</html>