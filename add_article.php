<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'journalist') {
    header('Location: login.php');
    exit();
}

require_once 'db.php'; // Assumes $conn = new mysqli...

$username = htmlspecialchars($_SESSION['username']);
$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $tags_input = trim($_POST['tags'] ?? '');
    $author_id = $_SESSION['user_id'];

    if (empty($title) || empty($content)) {
        $error = 'Title and content are required.';
    } else {
        $image_url = null;

        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Error uploading image.';
            } elseif (!in_array($_FILES['image']['type'], $allowed_types)) {
                $error = 'Only JPG, PNG, GIF, and WEBP images are allowed.';
            } else {
                $upload_dir = __DIR__ . '/uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('img_') . '.' . $ext;
                $destination = $upload_dir . $filename;

                if (move_uploaded_file($_FILES['image']['tmp_name'], $destination)) {
                    $image_url = 'uploads/' . $filename;
                } else {
                    $error = 'Failed to move uploaded file.';
                }
            }
        }

        if (!$error) {
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO articles (title, content, image_url, author_id, status) VALUES (?, ?, ?, ?, 'draft')");
                $stmt->bind_param("sssi", $title, $content, $image_url, $author_id);
                $stmt->execute();
                $article_id = $conn->insert_id;
                $stmt->close();

                if (!empty($tags_input)) {
                    $tags = array_unique(array_filter(array_map('trim', explode(',', $tags_input))));
                    foreach ($tags as $tag) {
                        $tag_stmt = $conn->prepare("SELECT tag_id FROM tags WHERE name = ?");
                        $tag_stmt->bind_param("s", $tag);
                        $tag_stmt->execute();
                        $tag_stmt->store_result();
                        $tag_id = null;

                        if ($tag_stmt->num_rows > 0) {
                            $tag_stmt->bind_result($tag_id);
                            $tag_stmt->fetch();
                        }
                        $tag_stmt->close();

                        if (!$tag_id) {
                            $insert_tag_stmt = $conn->prepare("INSERT INTO tags (name) VALUES (?)");
                            $insert_tag_stmt->bind_param("s", $tag);
                            $insert_tag_stmt->execute();
                            $tag_id = $conn->insert_id;
                            $insert_tag_stmt->close();
                        }

                        $link_stmt = $conn->prepare("INSERT INTO article_tags (article_id, tag_id) VALUES (?, ?)");
                        $link_stmt->bind_param("ii", $article_id, $tag_id);
                        $link_stmt->execute();
                        $link_stmt->close();
                    }
                }

                $conn->commit();
                $success = 'Article submitted successfully and saved as draft.';
                $title = $content = $tags_input = '';
            } catch (Exception $e) {
                $conn->rollback();
                $error = "Error submitting article: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Article</title>
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

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            max-width: 800px;
            margin: auto;
        }
    </style>
</head>

<body>

    <!-- Top Navbar -->
    <div class="top-navbar">
        <button class="hamburger" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="m-0">Add Article</h4>
        <span class="text-muted d-none d-md-inline"><?php echo $username; ?></span>
    </div>

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <h4 class="text-center text-light mt-3">Journalist Panel</h4>
        <ul class="list-unstyled mt-4">
            <li><a href="journalist_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li class="active"><a href="add_article.php"><i class="fas fa-plus me-2"></i> Add Article</a></li>
            <li><a href="view_articles.php"><i class="fas fa-newspaper me-2"></i> View All Articles</a></li>
            <li><a href="edit_profile.php"><i class="fas fa-user-edit me-2"></i> Edit Profile</a></li>
            <li><a href="j.manage_comment.php"><i class="fas fa-sign-out-alt me-2"></i> Manage Comment</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main id="mainContent" class="main-content">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="form-container">
            <form action="add_article.php" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Article Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required value="<?php echo htmlspecialchars($title ?? ''); ?>">
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="content" name="content" rows="8" required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="image" class="form-label">Upload Image (optional)</label>
                    <input type="file" class="form-control" id="image" name="image" accept="image/*">
                </div>

                <div class="mb-3">
                    <label for="tags" class="form-label">Tags (comma-separated, optional)</label>
                    <input type="text" class="form-control" id="tags" name="tags" placeholder="e.g., Politics,Economy,Asia" value="<?php echo htmlspecialchars($tags_input ?? ''); ?>">
                </div>

                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Submit Article</button>
            </form>
        </div>
    </main>

    <!-- Sidebar Toggle Script -->
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