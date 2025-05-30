<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'journalist') {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $article_id = (int)$_GET['delete'];

    $stmt = $conn->prepare("SELECT article_id FROM articles WHERE article_id = ? AND author_id = ?");
    $stmt->bind_param("ii", $article_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();

        $conn->prepare("DELETE FROM article_tags WHERE article_id = ?")->bind_param("i", $article_id)->execute();
        $del_article = $conn->prepare("DELETE FROM articles WHERE article_id = ?");
        $del_article->bind_param("i", $article_id);

        if ($del_article->execute()) {
            $success = "Article deleted successfully.";
        } else {
            $error = "Failed to delete the article.";
        }
        $del_article->close();
    } else {
        $error = "Article not found or unauthorized.";
    }
    $stmt->close();
}

$stmt = $conn->prepare("SELECT article_id, title, status, created_at FROM articles WHERE author_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Articles</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

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

        .badge {
            font-size: 13px;
            padding: 5px 10px;
            border-radius: 20px;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- Top Navbar -->
    <div class="top-navbar">
        <button class="hamburger" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="m-0">My Articles</h4>
        <span class="text-muted d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
    </div>

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <h4 class="text-center text-light mt-3">Journalist Panel</h4>
        <ul class="list-unstyled mt-4">
            <li><a href="journalist_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="add_article.php"><i class="fas fa-plus me-2"></i> Add Article</a></li>
            <li class="active"><a href="view_articles.php"><i class="fas fa-newspaper me-2"></i> View All Articles</a></li>
            <li><a href="edit_profile.php"><i class="fas fa-user-edit me-2"></i> Edit Profile</a></li>
            <li><a href="j.manage_comment.php"><i class="fas fa-sign-out-alt me-2"></i> Manage Comment</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main id="mainContent" class="main-content">
        <h1>Your Articles</h1>

        <!-- Toast Messages -->
        <?php if ($success): ?>
            <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3" id="successToast" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><?php echo htmlspecialchars($success); ?></div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="toast align-items-center text-bg-danger border-0 position-fixed top-0 end-0 m-3" id="errorToast" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><?php echo htmlspecialchars($error); ?></div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table id="articlesTable" class="table table-hover table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($article = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($article['title']); ?></td>
                                <td>
                                    <?php
                                    $status = $article['status'];
                                    $badge_class = match ($status) {
                                        'draft' => 'warning',
                                        'published' => 'success',
                                        'archived' => 'dark',
                                        default => 'secondary',
                                    };
                                    ?>
                                    <span class="badge bg-<?php echo $badge_class; ?>"><?php echo ucfirst($status); ?></span>
                                </td>
                                <td><?php echo date('Y-m-d H:i', strtotime($article['created_at'])); ?></td>
                                <td>
                                    <a href="view_article.php?id=<?php echo $article['article_id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="edit_article.php?id=<?php echo $article['article_id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>

                                    <!-- Delete button with modal -->
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $article['article_id']; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- Modal -->
                                    <div class="modal fade" id="deleteModal<?php echo $article['article_id']; ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?php echo $article['article_id']; ?>" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteModalLabel<?php echo $article['article_id']; ?>">Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete <strong><?php echo htmlspecialchars($article['title']); ?></strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <a href="view_articles.php?delete=<?php echo $article['article_id']; ?>" class="btn btn-danger">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No articles found. <a href="add_article.php">Add your first article</a>.</p>
        <?php endif; ?>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('show');
        }

        $(document).ready(function() {
            $('#articlesTable').DataTable({
                responsive: true,
                pageLength: 5,
                columnDefs: [{
                    orderable: false,
                    targets: 3
                }]
            });

            const toastElSuccess = document.getElementById('successToast');
            if (toastElSuccess) new bootstrap.Toast(toastElSuccess).show();

            const toastElError = document.getElementById('errorToast');
            if (toastElError) new bootstrap.Toast(toastElError).show();
        });
    </script>

</body>

</html>