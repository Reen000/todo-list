<?php
session_start();
include 'config.php'; // Include your database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Save user data to the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $password);
    
    if ($stmt->execute()) {
        $_SESSION['username'] = $username; // Store username in session
        header("Location: login.php"); // Redirect to login page after successful signup
        exit();
    } else {
        $error = "Failed to create account.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Sign Up Slider</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="signin-signup">
                <!-- Sign Up Form -->
                <form action="signup.php" class="sign-up-form" method="POST">
                    <h2 class="title">Sign Up</h2>
                    <div class="input-field">
                        <i class="fas fa-user"></i>
                        <input type="text" placeholder="Username" name="username" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" placeholder="Email" name="email" required />
                    </div>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" placeholder="Password" name="password" required />
                    </div>
                    <input type="submit" value="Sign Up" class="btn solid" />
                </form>
            </div>
        </div>

        <!-- Panels for Sliding Effect -->
        <div class="panels-container">
            <div class="panel right-panel">
                <div class="content">
                    <h3>One of us?</h3>
                    <p>If you already have an account, login now.</p>
                    <button class="btn transparent" id="loginButton">Login</button>
                </div>
            </div>
        </div>
    </div>

    <script src="scripts.js"></script>
</body>
</html>