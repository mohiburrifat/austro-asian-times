<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Login | Austro-Asian Times</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #e6e6fa, #f3e8ff);
      /* Lavender shades */
      color: #333;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 1rem;
    }

    .card {
      width: 100%;
      max-width: 420px;
      border-radius: 16px;
      padding: 2rem;
      box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05);
      background-color: #ffffff;
      border: 1px solid #dee2e6;
    }

    .card h2 {
      font-weight: 600;
      color: #6f42c1;
      /* Purple tone for headers */
    }

    .form-label {
      font-weight: 500;
    }

    .form-control,
    .form-select {
      border-radius: 10px;
      background-color: #f8f9fa;
      border: 1px solid #ced4da;
    }

    .form-control:focus,
    .form-select:focus {
      background-color: #fff;
      border-color: #b37cd9;
      /* Lavender focus color */
      box-shadow: 0 0 0 0.2rem rgba(179, 124, 217, 0.25);
    }

    .btn-primary {
      background-color: #b37cd9;
      /* Lavender button */
      border: none;
      border-radius: 10px;
      font-weight: 500;
    }

    .btn-primary:hover {
      background-color: #a55edc;
    }

    .alert {
      background-color: #f8d7da;
      color: #842029;
      border: none;
      border-radius: 10px;
    }
  </style>
</head>

<body>

  <div class="card">
    <h2 class="text-center mb-4">Login</h2>

    <!-- Error Message -->
    <?php if (isset($_GET['error'])): ?>
      <div class="alert text-center"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <form action="login_process.php" method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" required placeholder="Enter username">
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password">
      </div>

      <div class="mb-3">
        <label for="role" class="form-label">Login as</label>
        <select class="form-select" name="role" id="role" required>
          <option value="">Select Role</option>
          <option value="journalist">Journalist</option>
          <option value="editor">Editor</option>
        </select>
      </div>

      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Login</button>
      </div>
    </form>
  </div>

</body>

</html>