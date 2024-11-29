<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'users');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize input
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];

    // Check credentials
    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            echo "<script>alert('Login Successful'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<div class='alert alert-danger'>Invalid Password</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>No account found with that email</div>";
    }
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            
            background: url('./background.png') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
        }
        .login-card {
            max-width: 400px;
            margin: 4rem auto;
            padding: 2rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            background: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            color: #333;
        }
        .login-card h2 {
            margin-bottom: 1.5rem;
            color: #333;
            text-align: center;
        }
        .icon-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        .icon-container img {
            width: 80px;
            height: 80px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="login-card">
        <div class="icon-container">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Icon">
        </div>
        <h2>Login</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-success btn-block">Login</button>
            </div>
        </form>
        <p class="text-center mt-3">
            Don’t have an account? <a href="register.php" class="text-success">Register here</a>.
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
