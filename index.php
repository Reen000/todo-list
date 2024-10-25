<?php
session_start();
include 'config.php'; // Include your database configuration

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Fetch user ID
$stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username");
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$user_id = $user['id'];

// Fetch user's to-do lists
$stmt = $pdo->prepare("SELECT * FROM todo_lists WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Separate completed and incomplete tasks
$completed_tasks = [];
$incomplete_tasks = [];

foreach ($lists as $list) {
    if ($list['is_completed']) {
        $completed_tasks[] = $list;
    } else {
        $incomplete_tasks[] = $list;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=1.0">
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mt-5">Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

        <div class="row">
        <div class="col-md-6 mb-4">
                <h2>Your Incomplete Tasks</h2>
                <ul class="list-group shadow-sm">
                    <?php foreach ($incomplete_tasks as $task): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="viewlist.php?id=<?php echo $task['id']; ?>" class="text-decoration-none text-incomplete fw-bold"><?php echo htmlspecialchars($task['title']); ?></a>
                                <span class="text-muted d-block"><?php echo htmlspecialchars($task['description']); ?></span> <!-- Display description -->
                            </div>
                            <div>
                            <form action="completelist.php" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                <button type="submit" class="btn btn-success btn-sm">Complete</button>
                            </form>
                                <a href="deletelist.php?id=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm ms-2">Delete</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-md-6 mb-4">
                <h2>Your Completed Tasks</h2>
                <ul class="list-group shadow-sm">
                    <?php foreach ($completed_tasks as $task): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <a href="viewlist.php?id=<?php echo $task['id']; ?>" class="text-decoration-none text-success fw-bold"><?php echo htmlspecialchars($task['title']); ?></a>
                                <span class="text-muted d-block"><?php echo htmlspecialchars($task['description']); ?></span> <!-- Display description -->
                            </div>
                            <a href="deletelist.php?id=<?php echo $task['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="text-center mt-3">
            <a href="createlist.php" class="btn btn-primary me-2">Create New List</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
<script>
function confirmComplete(taskId) {
    if (confirm("Are you sure you want to mark this task as complete?")) {
        $.ajax({
            url: 'completelist.php',
            method: 'POST',
            data: { id: taskId },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    moveTaskToCompleted(taskId);
                } else {
                    alert("Error: " + (result.error || "Unable to complete task."));
                }
            }
        });
    }
}

function moveTaskToCompleted(taskId) {
    // Find the task in the incomplete list and move it to the completed list
    const taskElement = $("a[href='viewlist.php?id=" + taskId + "']").closest('li');
    $('#completed-task-list').append(taskElement);
    taskElement.find('.btn-success').remove(); // Remove "Complete" button
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>