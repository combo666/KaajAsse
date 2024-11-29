<?php include('../../conf/database/db_connect.php'); ?>

<?php
// Collect inputs with basic sanitization
$task_name = $_POST['task_name'] ?? null;
$task_date = $_POST['task_date'] ?? null;
$task_duration = isset($_POST['task_duration']) ? intval($_POST['task_duration']) : null;
$assigned_user = isset($_POST['assigned_user']) ? intval($_POST['assigned_user']) : null;
$task_description = $_POST['task_description'] ?? null;

// Validate required fields
if (!$task_name || !$task_date || !$task_duration || !$assigned_user || !$task_description) {
    echo json_encode(['success' => false, 'error' => 'Invalid input data. All fields are required.']);
    exit;
}

// Prepare the statement
$stmt = $connect->prepare("INSERT INTO task_calendar (task_name, task_start_date, task_duration, assigned_user, task_description) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param('ssiss', $task_name, $task_date, $task_duration, $assigned_user, $task_description);

// Execute the statement
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $stmt->error]);
}
?>
