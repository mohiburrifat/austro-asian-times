<?php
session_start();
require_once 'db.php';

// Validate article ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(404);
    echo "<h1>Article not found</h1>";
    exit();
}

$article_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'] ?? null;
$user_role = $_SESSION['role'] ?? null;

// Check if comments are enabled
$comments_enabled = true;
$setting_stmt = $conn->prepare("SELECT comments_enabled FROM comment_settings WHERE article_id = ?");
$setting_stmt->bind_param("i", $article_id);
$setting_stmt->execute();
$setting_stmt->bind_result($comments_enabled_val);
if ($setting_stmt->fetch()) {
    $comments_enabled = (bool)$comments_enabled_val;
}
$setting_stmt->close();

// Handle new comment submission
$comment_submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $comments_enabled && isset($_POST['name'], $_POST['content'])) {
    $name = trim($_POST['name']);
    $content = trim($_POST['content']);

    if ($name && $content) {
        $stmt = $conn->prepare("INSERT INTO comments (article_id, name, content, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("iss", $article_id, $name, $content);
        $stmt->execute();
        $stmt->close();
        $comment_submitted = true;
    }
}

// Fetch article
$stmt = $conn->prepare("SELECT a.title, a.content, a.image_url, a.created_at, a.updated_at, a.status, a.author_id, u.username 
                        FROM articles a 
                        JOIN users u ON a.author_id = u.user_id 
                        WHERE a.article_id = ?");
$stmt->bind_param("i", $article_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    http_response_code(404);
    echo "<h1>Article not found</h1>";
    exit();
}

$stmt->bind_result($title, $content, $image_url, $created_at, $updated_at, $status, $author_id, $author_username);
$stmt->fetch();
$stmt->close();

// Access control
$can_view = false;
if ($user_role === 'editor') {
    $can_view = true;
} elseif ($user_role === 'journalist') {
    if ($author_id == $user_id || $status === 'approved') {
        $can_view = true;
    }
} elseif ($status === 'approved') {
    $can_view = true;
}

if (!$can_view) {
    http_response_code(404);
    echo "<h1>Article not found</h1>";
    exit();
}

// Get approved comments
$comments = [];
$cstmt = $conn->prepare("SELECT name, content, created_at FROM comments WHERE article_id = ? AND status = 'approved' ORDER BY created_at DESC");
$cstmt->bind_param("i", $article_id);
$cstmt->execute();
$cstmt->bind_result($c_name, $c_content, $c_created);
while ($cstmt->fetch()) {
    $comments[] = ['name' => $c_name, 'content' => $c_content, 'created_at' => $c_created];
}
$cstmt->close();

// Always go back to index
$back_url = 'index.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            padding: 30px;
            max-width: 900px;
            margin: auto;
        }

        .article-image {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .back-btn {
            margin-bottom: 20px;
        }

        .article-meta {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .comment-box {
            margin-top: 40px;
        }

        .comment {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 15px;
            background-color: #fff;
        }

        .comment small {
            color: #777;
        }
    </style>
</head>

<body>

    <a href="<?php echo htmlspecialchars($back_url); ?>" class="btn btn-secondary back-btn">&larr; Back</a>

    <h1><?php echo htmlspecialchars($title); ?></h1>

    <div class="article-meta">
        By <strong><?php echo htmlspecialchars($author_username); ?></strong> |
        Published on <?php echo date('F j, Y, g:i a', strtotime($created_at)); ?>
        <?php if ($updated_at !== $created_at): ?>
            <br>
            <small><em>Edited at <?php echo date('F j, Y, g:i a', strtotime($updated_at)); ?></em></small>
        <?php endif; ?>
    </div>

    <?php if ($image_url): ?>
        <img src="<?php echo htmlspecialchars($image_url); ?>" alt="Article Image" class="article-image" />
    <?php endif; ?>

    <div class="article-content" style="white-space: pre-wrap;">
        <?php echo nl2br(htmlspecialchars($content)); ?>
    </div>

    <!-- Comment Form -->
    <div class="comment-box">
        <h3>Leave a Comment</h3>
        <?php if (!empty($comment_submitted)): ?>
            <div class="alert alert-success">Thank you! Your comment is awaiting moderation.</div>
        <?php endif; ?>

        <?php if ($comments_enabled): ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Your Name</label>
                    <input name="name" id="name" class="form-control" required maxlength="100">
                </div>
                <div class="mb-3">
                    <label for="content" class="form-label">Your Comment</label>
                    <textarea name="content" id="content" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Submit Comment</button>
            </form>
        <?php else: ?>
            <div class="alert alert-warning">Commenting is currently disabled for this article.</div>
        <?php endif; ?>
    </div>

    <!-- Display Approved Comments -->
    <div class="comment-box">
        <h3 class="mt-5">Comments</h3>
        <?php if (empty($comments)): ?>
            <p>No comments yet.</p>
        <?php else: ?>
            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <strong><?php echo htmlspecialchars($comment['name']); ?></strong><br>
                    <small><?php echo date('F j, Y, g:i a', strtotime($comment['created_at'])); ?></small>
                    <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>

</html>