<?php
session_start();
require_once 'C:\xampp\htdocs\task-collab-app\db.php'; // Ensure the path is correct

// Redirect to login page if not logged in or if the user is not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle task deletion
if (isset($_GET['delete_task'])) {
    $task_id = $_GET['delete_task'];

    // Ensure the task_id is valid and exists
    if (is_numeric($task_id)) {
        // Delete the task
        $delete_query = "DELETE FROM tasks WHERE id = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("i", $task_id);
        
        if ($stmt->execute()) {
            // Redirect back to the admin dashboard after successful deletion
            header("Location: admin-dashboard.php?message=Task deleted successfully.");
            exit();
        } else {
            // If deletion fails
            header("Location: admin-dashboard.php?error=Failed to delete task.");
            exit();
        }
    } else {
        // If the task_id is not valid
        header("Location: admin-dashboard.php?error=Invalid task ID.");
        exit();
    }
} else {
    // If no task_id is passed
    header("Location: admin-dashboard.php?error=No task ID provided.");
    exit();
}
?>
