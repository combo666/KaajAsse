<?php
include('../../conf/database/db_connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Access denied.']);
    exit;
}

// Validate the form submission 
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Fetch form data
$task_option = $_POST['task_option'] ?? null;
$task_name = $_POST['task_name'] ?? null;
$new_task_name = $_POST['new_task_name'] ?? null;
$task_date = $_POST['task_date'] ?? null;
$task_duration = $_POST['task_duration'] ?? null;
$task_description = $_POST['task_description'] ?? null;
$task_color = $_POST['task_color'] ?? null;
$task_priority = $_POST['task_priority'] ?? 'low'; // Default to 'low' if not provided
$assigned_users = $_POST['assigned_user'] ?? [];

try {
    if ($task_option === 'new') {
        // Validate new task fields
        if (empty($new_task_name) || empty($task_date) || empty($task_duration) || empty($task_color)) {
            echo json_encode(['success' => false]);
            exit;
        }
    
        // Check if the task already exists
        $check_task_query = "SELECT COUNT(*) AS count FROM KaajAsse.task_calendar WHERE task_name = ? AND task_start_date = ?";
        $stmt = $connect->prepare($check_task_query);
        $stmt->bind_param("ss", $new_task_name, $task_date);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
    
        if ($result['count'] > 0) {
            echo json_encode(['success' => false]);
            exit;
        }
    
        // Insert the new task into `task_calendar` with default status 'backlog'
        $insert_task_query = "INSERT INTO KaajAsse.task_calendar (task_name, task_start_date, task_duration, task_description, task_color, task_priority, task_status)
                              VALUES (?, ?, ?, ?, ?, ?, 'backlog')";
        $stmt = $connect->prepare($insert_task_query);
        $stmt->bind_param("ssisss", $new_task_name, $task_date, $task_duration, $task_description, $task_color, $task_priority);
        $stmt->execute();
    
        // Get the newly inserted task ID
        $new_task_id = $stmt->insert_id;
    
        // Assign users to the new task in `task_user`
        if (!empty($assigned_users)) {
            $assign_user_query = "INSERT INTO KaajAsse.task_user (task_id, user_id) VALUES (?, ?)";
            $stmt = $connect->prepare($assign_user_query);
            foreach ($assigned_users as $user_id) {
                $stmt->bind_param("ii", $new_task_id, $user_id);
                $stmt->execute();
            }
        }
    
        echo json_encode(['success' => true]);
    }
    elseif ($task_option === 'existing') {
        // Validate existing task and users
        if (empty($task_name) || empty($assigned_users)) {
            echo json_encode(['success' => false]);
            exit;
        }

        // Assign users to the existing task in `task_user` without duplicates
        foreach ($assigned_users as $user_id) {
            // Check if the user is already assigned to the task
            $check_user_query = "SELECT COUNT(*) AS count FROM KaajAsse.task_user WHERE task_id = ? AND user_id = ?";
            $stmt = $connect->prepare($check_user_query);
            $stmt->bind_param("ii", $task_name, $user_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if ($result['count'] == 0) {
                // If the user is not already assigned, assign them to the task
                $assign_user_query = "INSERT INTO KaajAsse.task_user (task_id, user_id) VALUES (?, ?)";
                $stmt = $connect->prepare($assign_user_query);
                $stmt->bind_param("ii", $task_name, $user_id);
                $stmt->execute();
            }
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} catch (Exception $e) {
    // Log error instead of sending it to the frontend
    error_log($e->getMessage(), 3, '../../logs/error.log');
    echo json_encode(['success' => false]);
}
?>
