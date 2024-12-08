<?php
include('../../conf/database/db_connect.php');

// Collect inputs
$task_option = $_POST['task_option'];
$task_id = null;

if ($task_option === 'existing') {
    // Use existing task
    $task_id = intval($_POST['task_name']);
} else {
    // Create a new task
    $task_name = $_POST['new_task_name'];
    $task_date = $_POST['task_date'];
    $task_duration = intval($_POST['task_duration']);
    $task_description = $_POST['task_description'];
    $task_color = $_POST['task_color'];

    // Insert new task into database
    $stmt = $connect->prepare("INSERT INTO task_calendar (task_name, task_start_date, task_duration, task_description, task_color) 
                               VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param('ssiss', $task_name, $task_date, $task_duration, $task_description, $task_color);
    $stmt->execute();
    $task_id = $connect->insert_id;
}

// Assign users to the task
$assigned_users = $_POST['assigned_user']; // Array of user IDs
if ($task_id && !empty($assigned_users)) {
    $connect->query("DELETE FROM task_user WHERE task_id = $task_id"); // Clear existing assignments
    $assign_stmt = $connect->prepare("INSERT INTO task_user (task_id, user_id) VALUES (?, ?)");
    foreach ($assigned_users as $user_id) {
        $assign_stmt->bind_param('ii', $task_id, $user_id);
        $assign_stmt->execute();
    }
}

echo json_encode(['success' => true]);
?>
