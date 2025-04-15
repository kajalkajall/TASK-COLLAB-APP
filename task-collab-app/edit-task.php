<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

$taskId = $_GET['id'] ?? null;
$userId = $_SESSION['user_id'];
$error = '';
$success = '';

// Fetch task details
if ($taskId) {
    $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $taskId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $task = $result->fetch_assoc();

    if (!$task) {
        $error = "Task not found.";
    }
} else {
    $error = "Invalid task ID.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $deadline = $_POST['deadline'];
    $priority = $_POST['priority'];
    $status = $_POST['status'];

    if (!empty($title) && !empty($deadline) && !empty($priority) && !empty($status)) {
        $stmt = $conn->prepare("UPDATE tasks SET title = ?, deadline = ?, priority = ?, status = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ssssii", $title, $deadline, $priority, $status, $taskId, $userId);
        if ($stmt->execute()) {
            header("Location: u_dashboard.php");
            exit();
        } else {
            $error = "Failed to update task.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Task - Task App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h4 class="card-title text-center mb-4">Edit Task</h4>

                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php elseif ($task): ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="title" class="form-label">Task Title</label>
                                <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($task['title']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="deadline" class="form-label">Deadline</label>
                                <input type="date" name="deadline" id="deadline" class="form-control" value="<?= htmlspecialchars($task['deadline']) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="High" <?= $task['priority'] === 'High' ? 'selected' : '' ?>>High</option>
                                    <option value="Medium" <?= $task['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="Low" <?= $task['priority'] === 'Low' ? 'selected' : '' ?>>Low</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select" required>
                                    <option value="Pending" <?= $task['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="In Progress" <?= $task['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="Completed" <?= $task['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-dark">Update Task</button>
                                <a href="u_dashboard.php" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>