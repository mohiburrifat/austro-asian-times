<?php
session_start();

// Check if editor is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'editor') {
    header("Location: login.php");
    exit();
}

require 'db.php';

$message = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, 'journalist')");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Journalist added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dashboard | Austro-Asian Times</title>
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
            --gradient: linear-gradient(to right, #6fb1fc, #4e90e2);
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

        .navbar-toggler {
            background: none;
            border: none;
            font-size: 1.4rem;
            color: var(--text-dark);
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

        .card {
            background: var(--card-bg);
            border: none;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .card i {
            color: var(--primary);
        }

        .btn-gradient {
            background: var(--gradient);
            border: none;
            color: white;
            font-weight: bold;
            padding: 10px 20px;
            border-radius: 5px;
            transition: 0.3s ease;
        }

        .btn-gradient:hover {
            opacity: 0.9;
        }

        .form-label {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <nav class="navbar">
        <button class="navbar-toggler" id="toggleSidebar"><i class="fas fa-bars"></i></button>
        <div class="navbar-brand">Austro-Asian Times</div>
    </nav>

    <!-- Sidebar -->
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
            <li><a href="articles.php"><i class="fas fa-newspaper me-2"></i> Articles</a></li>
            <li><a href="pending_articles.php"><i class="fas fa-clock me-2"></i> Pending Approval</a></li>
            <li><a href="journalists.php"><i class="fas fa-users me-2"></i> Journalists</a></li>
            <li class="active"><a href="add_journalist.php"><i class="fas fa-user-plus me-2"></i> Add Journalist</a></li>
            <li><a href="manage_comment.php"><i class="fas fa-comments me-2"></i> Manage Comments</a></li>
            <li><a href="comment_settings.php"><i class="fas fa-comment me-2"></i> Comment Settings</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </div>

    <!-- Overlay for mobile -->
    <div class="overlay" id="overlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Add New Journalist</h2>
            <div class="text-muted"><i class="fas fa-user-circle me-2"></i> <?= htmlspecialchars($_SESSION['username']) ?> (Editor)</div>
        </div>
        <?= $message ?>
        <div class="card">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input required type="text" name="username" class="form-control" id="username" placeholder="Enter username">
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input required type="email" name="email" class="form-control" id="email" placeholder="Enter email">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input required type="password" name="password" class="form-control" id="password" placeholder="Enter password">
                </div>
                <button type="submit" class="btn btn-gradient">Add Journalist</button>
            </form>
        </div>
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