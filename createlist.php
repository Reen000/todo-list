<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['title'])) {
    $title = $_POST['title'];

    // Get the user's ID
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
    $stmt->bindParam(':username', $_SESSION['username']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_id = $user['id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description']; // Get the description

        $stmt = $pdo->prepare("INSERT INTO todo_lists (title, description, user_id, status) VALUES (:title, :description, :user_id, 'incomplete')");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description); // Bind the description
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();

        header("Location: index.php");
        exit();
    }

    if ($stmt->fetchColumn() > 0) {
        $error = "A list with this title already exists.";
    } else {
        // Insert new to-do list
        $stmt = $pdo->prepare("INSERT INTO todo_lists (user_id, title) VALUES (:user_id, :title)");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':title', $title);
        $stmt->execute();

        header("Location: viewlist.php?success=1");
        exit();
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description']; // Get the description

    $stmt = $pdo->prepare("INSERT INTO todo_lists (title, description, user_id, status) VALUES (:title, :description, :user_id, 'incomplete')");
    $stmt->bindParam(':title', $title);
    $stmt->bindParam(':description', $description); // Bind the description
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();

    header("Location: index.php");
    exit();
}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1.0">
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Create a New To-Do List</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form action="createlist.php" method="POST">
    <div class="mb-3">
        <label for="title" class="form-label">Task Title</label>
        <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Task Description</label>
        <textarea name="description" class="form-control" rows="3" required></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Create Task</button>
</form>
        <div class="text-center mt-3">
            <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>
</body>
</html>