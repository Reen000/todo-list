<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $list_id = $_GET['id'];

    // Fetch the list title for confirmation
    $stmt = $pdo->prepare("SELECT title FROM todo_lists WHERE id = :id");
    $stmt->bindParam(':id', $list_id);
    $stmt->execute();
    $list = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the list exists
    if (!$list) {
        echo "To-Do List not found.";
        exit();
    }
} else {
    echo "Invalid request.";
    exit();
}

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("DELETE FROM todo_lists WHERE id = :id");
    $stmt->bindParam(':id', $list_id);
    $stmt->execute();
    header("Location: viewlist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1.0">
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Confirm Deletion</h1>
        <p class="text-center">Are you sure you want to delete the list titled "<strong><?php echo htmlspecialchars($list['title']); ?></strong>"?</p>
        <form action="deletelist.php?id=<?php echo $list_id; ?>" method="POST" class="text-center">
            <button type="submit" class="btn btn-danger">Yes, Delete</button>
            <a href="viewlist.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>