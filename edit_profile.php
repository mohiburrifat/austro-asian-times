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

$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($current_username, $current_email);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($username) || empty($email)) {
        $error = "Username and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $password_confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $stmt->bind_param("ssi", $username, $email, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or email already taken by another user.";
        } else {
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password_hash = ? WHERE user_id = ?");
                $update_stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
            } else {
                $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
                $update_stmt->bind_param("ssi", $username, $email, $user_id);
            }

            if ($update_stmt->execute()) {
                $success = "Profile updated successfully.";
                $_SESSION['username'] = $username;
                $current_username = $username;
                $current_email = $email;
            } else {
                $error = "Failed to update profile.";
            }

            $update_stmt->close();
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap & Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
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
                padding: 20px 10px;
            }

            .form-container {
                width: 100% !important;
            }
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>

<body>

    <!-- Top Navbar -->
    <div class="top-navbar">
        <button class="hamburger" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h4 class="m-0">Edit Profile</h4>
        <span class="text-muted d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
    </div>

    <!-- Sidebar -->
    <nav id="sidebar" class="sidebar">
        <h4 class="text-center text-light mt-3">Journalist Panel</h4>
        <ul class="list-unstyled mt-4">
            <li><a href="journalist_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a></li>
            <li><a href="add_article.php"><i class="fas fa-plus me-2"></i> Add Article</a></li>
            <li><a href="view_articles.php"><i class="fas fa-newspaper me-2"></i> View All Articles</a></li>
            <li class="active"><a href="edit_profile.php"><i class="fas fa-user-edit me-2"></i> Edit Profile</a></li>
            <li><a href="j.manage_comment.php"><i class="fas fa-sign-out-alt me-2"></i> Manage Comment</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main id="mainContent" class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">

                    <h1 class="text-center mb-4"></h1>

                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <div class="form-container mx-auto">
                        <form method="POST" action="edit_profile.php">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="username" name="username" required value="<?php echo htmlspecialchars($current_username); ?>" />
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($current_email); ?>" />
                            </div>

                            <hr />
                            <p class="text-muted">Leave password fields empty if you don't want to change your password.</p>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" />
                            </div>

                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" placeholder="Confirm new password" />
                            </div>

                            <button type="submit" class="btn btn-warning w-100"><i class="fas fa-save me-2"></i>Update Profile</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
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