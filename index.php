<?php
require_once 'db.php';

$tagsResult = $conn->query("SELECT * FROM tags");
$journalistsResult = $conn->query("SELECT user_id, username FROM users WHERE role = 'journalist'");

// Filters
$tagFilter = isset($_GET['tag_id']) ? intval($_GET['tag_id']) : null;
$authorFilter = isset($_GET['author_id']) ? intval($_GET['author_id']) : null;
$dateOrder = isset($_GET['date']) && $_GET['date'] === 'oldest' ? 'ASC' : 'DESC';

// Base query
$query = "
    SELECT a.article_id, a.title, a.content, a.image_url, a.created_at, a.updated_at,
           u.username AS author
    FROM articles a
    LEFT JOIN users u ON a.author_id = u.user_id
";

// Apply filters
$conditions = ["a.status = 'approved'"];
if ($tagFilter) {
    $query .= " JOIN article_tags at ON a.article_id = at.article_id";
    $conditions[] = "at.tag_id = $tagFilter";
}
if ($authorFilter) {
    $conditions[] = "a.author_id = $authorFilter";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY a.updated_at $dateOrder LIMIT 12";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Austro-Asian Times</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: rgb(241, 249, 242);
            padding-top: 4.5rem;
            font-family: 'Segoe UI', sans-serif;
        }

        .navbar {
            background-color: #003049 !important;
        }

        .navbar-brand,
        .nav-link,
        .btn-outline-light {
            color: #ffffff !important;
        }

        .offcanvas {
            background-color: rgb(224, 224, 224);
        }

        .article-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .article-card:hover {
            transform: translateY(-6px);
        }

        .article-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .article-content {
            padding: 1.2rem;
        }

        .article-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #003049;
        }

        .article-meta {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .btn-outline-primary {
            border-color: #003049;
            color: #003049;
        }

        .btn-outline-primary:hover {
            background-color: #003049;
            color: #fff;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg fixed-top shadow">
        <div class="container-fluid">
            <button class="btn btn-outline-light me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sideMenu">
                &#9776;
            </button>
            <a class="navbar-brand text-white" href="index.php">Austro-Asian Times</a>
            <a href="login.php" class="btn btn-outline-light text-white">Login</a>
        </div>
    </nav>

    <!-- SIDEMENU FILTER -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sideMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Options</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="index.php" class="d-grid gap-3">
                <!-- Tag Filter -->
                <div>
                    <label class="form-label">Tag</label>
                    <select name="tag_id" class="form-select">
                        <option value="">-- All Tags --</option>
                        <?php while ($tag = $tagsResult->fetch_assoc()): ?>
                            <option value="<?= $tag['tag_id'] ?>" <?= $tagFilter == $tag['tag_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($tag['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Journalist Filter -->
                <div>
                    <label class="form-label">Journalist</label>
                    <select name="author_id" class="form-select">
                        <option value="">-- All Journalists --</option>
                        <?php while ($author = $journalistsResult->fetch_assoc()): ?>
                            <option value="<?= $author['user_id'] ?>" <?= $authorFilter == $author['user_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($author['username']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="form-label">Sort By Date</label>
                    <select name="date" class="form-select">
                        <option value="newest" <?= $dateOrder === 'DESC' ? 'selected' : '' ?>>Newest First</option>
                        <option value="oldest" <?= $dateOrder === 'ASC' ? 'selected' : '' ?>>Oldest First</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </form>
        </div>
    </div>

    <!-- ARTICLE GRID -->
    <div class="container mt-4">
        <div class="row g-4">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($article = $result->fetch_assoc()): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="article-card">
                            <?php if (!empty($article['image_url'])): ?>
                                <img src="<?= htmlspecialchars($article['image_url']) ?>" class="article-image" alt="Article Image">
                            <?php endif; ?>
                            <div class="article-content">
                                <a href="view_article.php?id=<?= $article['article_id'] ?>" class="text-decoration-none">
                                    <h2 class="article-title"><?= htmlspecialchars($article['title']) ?></h2>
                                </a>
                                <div class="article-meta">
                                    <?= htmlspecialchars($article['author']) ?> | <?= date('M d, Y', strtotime($article['updated_at'])) ?>
                                </div>
                                <p><?= nl2br(htmlspecialchars(mb_strimwidth($article['content'], 0, 150, '...'))) ?></p>
                                <a href="view_article.php?id=<?= $article['article_id'] ?>" class="btn btn-sm btn-outline-primary mt-2">Read More</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-muted">No articles found with current filters.</p>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>