<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $conn = new mysqli('localhost', 'root', '', 'users');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize input
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration Successful'); window.location.href='login.php';</script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
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
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: url('./background.png') no-repeat center center fixed;
            background-size: cover;
        }
        .register-card {
            max-width: 500px;
            margin: 4rem auto;
            padding: 2rem;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            background: #fff;
            border-radius: 8px;
        }
        .register-card h2 {
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
    <div class="register-card">
        <div class="icon-container">
            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="User Icon">
        </div>
        <h2>Register</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter your full name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Create a strong password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-block">Register</button>
            </div>
        </form>
        <p class="text-center mt-3">
            Already have an account? <a href="login.php" class="text-primary">Login here</a>.
        </p>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
