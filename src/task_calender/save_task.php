<?php
include('../../conf/database/db_connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_email'])) {
    echo json_encode(['success' => false, 'message' => 'Access denied. Please log in.']);
    exit;
}

// Validate the form submission
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
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
$assigned_users = $_POST['assigned_user'] ?? [];

try {
    if ($task_option === 'new') {
        // Validate new task fields
        if (empty($new_task_name) || empty($task_date) || empty($task_duration) || empty($task_color)) {
            echo json_encode(['success' => false, 'message' => 'All fields are required for a new task.']);
            exit;
        }

        // Insert the new task into `task_calendar`
        $insert_task_query = "INSERT INTO KaajAsse.task_calendar (task_name, task_start_date, task_duration, task_description, task_color, task_priority, task_status)
                              VALUES (?, ?, ?, ?, ?, 'low', 'todo')";
        $stmt = $connect->prepare($insert_task_query);
        $stmt->bind_param("ssiss", $new_task_name, $task_date, $task_duration, $task_description, $task_color);
        if (!$stmt->execute()) {
            throw new Exception("Failed to create a new task: " . $stmt->error);
        }

        // Get the newly inserted task ID
        $new_task_id = $stmt->insert_id;

        // Assign users to the new task in `task_user`
        if (!empty($assigned_users)) {
            $assign_user_query = "INSERT INTO KaajAsse.task_user (task_id, user_id) VALUES (?, ?)";
            $stmt = $connect->prepare($assign_user_query);
            foreach ($assigned_users as $user_id) {
                $stmt->bind_param("ii", $new_task_id, $user_id);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to assign user ID $user_id to the task: " . $stmt->error);
                }
            }
        }

        // **Delete the last row from the `task_calendar` table**
        $delete_last_row_query = "DELETE FROM KaajAsse.task_calendar WHERE task_id = (SELECT MAX(task_id) FROM KaajAsse.task_calendar)";
        if (!$connect->query($delete_last_row_query)) {
            throw new Exception("Failed to delete the last task row: " . $connect->error);
        }

        echo json_encode(['success' => true, 'message' => 'New task created and users assigned successfully.']);
    } elseif ($task_option === 'existing') {
        // Validate existing task and users
        if (empty($task_name) || empty($assigned_users)) {
            echo json_encode(['success' => false, 'message' => 'Please select a task and assign users.']);
            exit;
        }

        // Assign users to the existing task in `task_user`
        $assign_user_query = "INSERT INTO KaajAsse.task_user (task_id, user_id) VALUES (?, ?)";
        $stmt = $connect->prepare($assign_user_query);
        foreach ($assigned_users as $user_id) {
            $stmt->bind_param("ii", $task_name, $user_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to assign user ID $user_id to the task: " . $stmt->error);
            }
        }

        // **Delete the last row from the `task_user` table**
        $delete_last_row_query = "DELETE FROM KaajAsse.task_user WHERE id = (SELECT MAX(id) FROM KaajAsse.task_user)";
        if (!$connect->query($delete_last_row_query)) {
            throw new Exception("Failed to delete the last task-user row: " . $connect->error);
        }

        echo json_encode(['success' => true, 'message' => 'Users assigned to the existing task successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid task option.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
