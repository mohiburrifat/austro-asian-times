<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'journalist') {
    header('Location: login.php');
    exit();
}
$username = htmlspecialchars($_SESSION['username']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Journalist Dashboard</title>
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

        .card {
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <!-- Top Navbar -->
    <div class="top-navbar">
        <button class="hamburger" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="m-0">Journalist Dashboard</h4>
        <span class="text-muted d-none d-md-inline"><?php echo $username; ?></span>
    </div>

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <h4 class="text-center text-light mt-3">Journalist Panel</h4>
        <ul class="list-unstyled mt-4">
            <li class="active"><a href="journalist_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="add_article.php"><i class="fas fa-plus me-2"></i> Add Article</a></li>
            <li><a href="view_articles.php"><i class="fas fa-newspaper me-2"></i> View All Articles</a></li>
            <li><a href="edit_profile.php"><i class="fas fa-user-edit me-2"></i> Edit Profile</a></li>
            <li><a href="j.manage_comment.php"><i class="fas fa-sign-out-alt me-2"></i> Manage Comment</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main id="mainContent" class="main-content">
        <h1>Welcome, <?php echo $username; ?> 👋</h1>
        <p class="lead">Manage your articles and update your profile from here.</p>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-plus fa-2x mb-2 text-primary"></i>
                        <h5 class="card-title">Add Article</h5>
                        <a href="add_article.php" class="btn btn-outline-primary btn-sm mt-2">Create New</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-newspaper fa-2x mb-2 text-success"></i>
                        <h5 class="card-title">View Articles</h5>
                        <a href="view_articles.php" class="btn btn-outline-success btn-sm mt-2">View All</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <i class="fas fa-user-edit fa-2x mb-2 text-warning"></i>
                        <h5 class="card-title">Edit Profile</h5>
                        <a href="edit_profile.php" class="btn btn-outline-warning btn-sm mt-2">Update Info</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript for Sidebar Toggle -->
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