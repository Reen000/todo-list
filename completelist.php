<?php
session_start();
include 'config.php'; // Include your database configuration

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_id = $_POST['id'];

    // Update the task status to completed
    $stmt = $pdo->prepare("UPDATE todo_lists SET is_completed = 1 WHERE id = :id");
    $stmt->bindParam(':id', $task_id);

    if ($stmt->execute()) {
        // Redirect back to the index page after completing the task
        header("Location: index.php");
        exit();
    } else {
        // Handle error if needed
        echo "Failed to complete task.";
    }
}
?>