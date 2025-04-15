<?php
session_start();
require_once 'db.php';

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$name = $_SESSION['name'];

// Fetch tasks
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY deadline ASC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$tasks = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard - Task App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100">

<nav class="navbar navbar-dark bg-primary shadow-sm">
    <div class="container">
        <span class="navbar-brand fs-4 fw-bold">Welcome, <?= htmlspecialchars($name) ?></span>
        <a href="logout.php" class="btn btn-light fw-semibold">Logout</a>
    </div>
</nav>

<div class="container py-4">
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="card-title mb-0">Your Tasks</h3>
                <a href="add-task.php" class="btn btn-success">+ Add Task</a>
            </div>

            <?php if ($tasks->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Title</th>
                                <th>Deadline</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($task = $tasks->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                    <td><?= htmlspecialchars($task['deadline']) ?></td>
                                    <td><?= htmlspecialchars($task['priority']) ?></td>
                                    <td><?= htmlspecialchars($task['status']) ?></td>
                                    <td>
                                        <a href="edit-task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                                        <a href="delete-task.php?id=<?= $task['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this task?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">No tasks yet. Click "Add Task" to get started.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>