<?php
session_start();
require_once 'db.php';

// Redirect if not logged in or not a user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Get the task ID from the URL
if (isset($_GET['id'])) {
    $taskId = $_GET['id'];
    $userId = $_SESSION['user_id'];

    // Prepare the delete query
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $taskId, $userId);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to the dashboard after deletion
        header("Location: u_dashboard.php");
        exit();
    } else {
        // Display an error message if the delete operation fails
        echo "<div class='alert alert-danger'>Failed to delete task.</div>";
    }
} else {
    // If no task ID is provided, redirect to the dashboard
    header("Location: dashboard.php");
    exit();
}
?>
