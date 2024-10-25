<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get user information from the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindParam(':username', $_SESSION['username']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user wants to update their profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Initialize an array to hold the fields to update
    $updateFields = [];
    $params = [':id' => $user['id']];

    // Check if the username has changed
    if ($username != $user['username']) {
        $updateFields[] = "username = :username";
        $params[':username'] = $username;
        $_SESSION['username'] = $username; // Update session username
    }

    // Check if the email has changed
    if ($email != $user['email']) {
        $updateFields[] = "email = :email";
        $params[':email'] = $email;
        $_SESSION['email'] = $email; // Update session email
    }

    // Check if the password has been changed
    if (!empty($password)) {
        $password = password_hash($password, PASSWORD_DEFAULT);
        $updateFields[] = "password = :password";
        $params[':password'] = $password;
    }

    // Only update if there are changes
    if (!empty($updateFields)) {
        $query = "UPDATE users SET " . implode(", ", $updateFields) . " WHERE id = :id";
        $stmt = $pdo->prepare($query);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
    }

    // Redirect to the profile page after updating
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1.0">
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">User  Profile</h1>
        <form action="viewlist.php" method="POST" class="mt-4">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
    </div>
</body>
</html>