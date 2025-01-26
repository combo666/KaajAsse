<?php
include('../../examples/includes/header.php'); 
include('../../conf/database/db_connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    die("Access denied. Please log in.");
}

// Fetch user data from the database
$email = $_SESSION['user_email'];
$query = "SELECT * FROM KaajAsse.user WHERE user_email = ?";
$stmt = $connect->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("User not found.");
}

$user = $result->fetch_assoc();
$_SESSION['role'] = $user['user_role']; // Store user role in session

// Include the Calendar class
include 'Calendar.php';

// Create a new calendar instance
$calendar = new Calendar();

// Fetch events from the database
$events_query = "
    SELECT 
        task_id,
        task_name,
        task_start_date,
        task_duration,
        task_color
    FROM KaajAsse.task_calendar
";
$events_result = $connect->query($events_query);

if (!$events_result) {
    die('Query failed: ' . $connect->error);
}


while ($event = $events_result->fetch_assoc()) {
    // Check if the user has permission to see this task (for 'u' role)
    if ($_SESSION['role'] === 'u') {
        // Fetch all task IDs assigned to the logged-in user
        $task_query = "
            SELECT task_id 
            FROM KaajAsse.task_user 
            WHERE user_id = ?
        ";
        $task_stmt = $connect->prepare($task_query);
        if (!$task_stmt) {
            error_log("Error preparing query: " . $connect->error);
            continue;
        }
    
        // Bind only the logged-in user's ID
        $task_stmt->bind_param("i", $_SESSION['user_id']);
        $task_stmt->execute();
    
        $task_result = $task_stmt->get_result();
    
        // Fetch task IDs into an array
        $assigned_task_ids = [];
        while ($row = $task_result->fetch_assoc()) {
            $assigned_task_ids[] = $row['task_id'];
        }
    
        // Check if the current event's task_id is in the array of assigned tasks
        if (!in_array($event['task_id'], $assigned_task_ids)) {
            error_log("Task ID " . $event['task_id'] . " is not assigned to User ID " . $_SESSION['user_id']);
            continue; // Skip tasks not assigned to this user
        } else {
            error_log("Task ID " . $event['task_id'] . " is assigned to User ID " . $_SESSION['user_id']);
        }
    }
    
    // Add the event to the calendar
    $calendar->add_event(
        $event['task_name'],
        $event['task_start_date'],
        $event['task_duration'],
        $event['task_color']
    );
    
}


?>

<div class="parent">
    <div class="div1"> <?= $calendar ?> </div>
    <?php if ($_SESSION['role'] === 'a') include 'popupForm.php'; ?>
    <div class="div2"> <?php include('todays_task.php'); ?> </div>
    <div class="div3"> <?php include('team_members.php'); ?> </div>
    <div style="margin-bottom: 50px ;"></div>
</div>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>
