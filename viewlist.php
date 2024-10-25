<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the user's ID
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
$stmt->bindParam(':username', $_SESSION['username']);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


// Pastikan $user tidak kosong sebelum menggunakan variabel

if ($user) {
    $user_id = $user['id']; // Ambil user_id dari hasil query

} else {
    // Jika pengguna tidak ditemukan, redirect atau tampilkan pesan error
    header("Location: login.php");
    exit();

}
// Fetch user's tasks or lists from the database
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
        if ($stmt->execute()) {
            // Optionally check for row count here
        }
    }

    // Redirect to the profile page after updating
    header("Location: viewlist.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM todo_lists WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Initialize filter and search variables
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the query based on filter and search
$query = "SELECT * FROM todo_lists WHERE user_id = :user_id";
$params = [':user_id' => $user_id];

if ($filter === 'completed') {
    $query .= " AND status = 'completed'";
} elseif ($filter === 'incomplete') {
    $query .= " AND status = 'incomplete'";
}

if (!empty($search)) {
    $query .= " AND title LIKE :search";
    $params[':search'] = '%' . $search . '%';
}

$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$lists = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your To-Do Lists</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=2.0">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Your To-Do Lists</h1>

        <!-- Profil Pengguna -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card shadow-sm p-3 mb-4 bg-white rounded">
                    <h4>Profil Pengguna</h4>
                    <p>Username: <?php echo htmlspecialchars($user['username']); ?></p>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                    <a href="profile.php" class="btn btn-primary w-100">Edit Profil</a>
                </div>
            </div>
            <!-- Daftar Tugas -->
            <div class="col-md-8">
                        <form action="viewlist.php" method="GET" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <input type="text" name="search" class="form-control" placeholder="Search tasks..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-4">
                                    <select name="filter" class="form-control">
                                        <option value="all" <?php if ($filter === 'all') echo 'selected'; ?>>All Tasks</option>
                                        <option value="completed" <?php if ($filter === 'completed') echo 'selected'; ?>>Completed Tasks</option>
                                        <option value="incomplete" <?php if ($filter === 'incomplete') echo 'selected'; ?>>Incomplete Tasks</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                                </div>
                            </div>
                        </form>

                        <table class="table table-hover mt-4">
                            <thead>
                                <tr>
                                    <th scope="col">List Title</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>


<tbody>
    <?php if (count($lists) > 0): ?>
        <?php foreach ($lists as $list): ?>
            <tr>
                <td><?php echo htmlspecialchars($list['title']); ?></td>
                <td class="<?php echo $list['is_completed'] ? 'text-success' : 'text-custom-incomplete'; ?>">
                    <?php echo $list['is_completed'] ? 'Completed' : 'Incomplete'; ?>
                </td>
                <td>
                    <a href="deletelist.php?id=<?php echo $list['id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="3" class="text-center">No to-do lists found.</td>
        </tr>
    <?php endif; ?>
</tbody>
                            </table>
                            <div class="text-center mt-3">
                                <a href="createlist.php" class="btn btn-primary">Create New List</a>
                                <a href="index.php" class="btn btn-secondary">Back to Dashboard</a>
                            </div>
                        </div>
                    </div>
                </div>