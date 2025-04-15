<?php
session_start();
require_once 'C:\xampp\htdocs\task-collab-app\db.php'; // Ensure the path is correct

// Redirect to login page if not logged in or if the user is not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all users and their tasks
$query = "SELECT users.id as user_id, users.name as user_name, tasks.id as task_id, tasks.title as task_title 
          FROM users 
          LEFT JOIN tasks ON users.id = tasks.user_id";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Handle task deletion
if (isset($_GET['delete_task'])) {
    $task_id = $_GET['delete_task'];
    
    // Delete the task
    $delete_query = "DELETE FROM tasks WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $task_id);
    if ($stmt->execute()) {
        echo "Task deleted successfully.";
    } else {
        echo "Failed to delete task.";
    }
}

// Fetch users and their tasks into an associative array
$users_tasks = [];
while ($row = $result->fetch_assoc()) {
    $users_tasks[$row['user_id']]['name'] = $row['user_name'];
    $users_tasks[$row['user_id']]['tasks'][] = [
        'task_id' => $row['task_id'],
        'task_title' => $row['task_title'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid px-0">
        <header class="bg-dark text-white py-4 mb-4">
            <h1 class="text-center">Welcome, Admin!</h1>
        </header>

        <div class="container">
            <h2 class="text-center mb-4 text-secondary">All Users and Their Tasks</h2>
            
            <div class="card shadow mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>User Name</th>
                                    <th>Task Title</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users_tasks as $user_id => $user_data): ?>
                                    <tr>
                                        <td class="fw-bold bg-light" rowspan="<?= count($user_data['tasks']) ?>"><?= htmlspecialchars($user_data['name']) ?></td>
                                        <?php foreach ($user_data['tasks'] as $index => $task): ?>
                                            <?php if ($index > 0) echo "<tr>"; ?>
                                                <td><?= htmlspecialchars($task['task_title']) ?></td>
                                                <td>
                                                    <a href="?delete_task=<?= $task['task_id'] ?>" 
                                                       class="btn btn-danger btn-sm" 
                                                       onclick="return confirm('Are you sure you want to delete this task?')">
                                                        Delete
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4 mb-5">
                <a href="../logout.php" class="btn btn-dark px-4 py-2">Logout</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>