<?php
include('../../conf/database/db_connect.php');

$task_id = intval($_GET['task_id']);
$response = ['success' => false];

if ($task_id) {
    $stmt = $connect->prepare("SELECT task_name, task_start_date, task_duration, task_description, task_color FROM task_calendar WHERE task_id = ?");
    $stmt->bind_param('i', $task_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
        $response = [
            'success' => true,
            'task_name' => $task['task_name'],
            'task_start_date' => $task['task_start_date'],
            'task_duration' => $task['task_duration'],
            'task_description' => $task['task_description'],
            'task_color' => $task['task_color']
        ];
    } else {
        $response['message'] = 'Task not found.';
    }
}

echo json_encode($response);
?>
