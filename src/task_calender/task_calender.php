<?php
include('../../examples/includes/header.php'); 
include('../../conf/database/db_connect.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['uname'])) {
    die("Access denied. Please log in.");
}

// Fetch user data from the database
$email = $_SESSION['uname'];
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
$events_query = "SELECT task_name, task_start_date, task_duration, task_color FROM KaajAsse.task_calendar";
$events_result = $connect->query($events_query);

while ($event = $events_result->fetch_assoc()) {
    // Debug: Ensure $event['task_color'] is correctly fetched
    error_log("Task: " . $event['task_name'] . ", Color: " . $event['task_color']); // Logs color for debugging
    $calendar->add_event($event['task_name'], $event['task_start_date'], $event['task_duration'], $event['task_color']);
}

?>

<div class="parent">
    <div class="div1"> <?= $calendar ?> </div>
    <?php if ($_SESSION['role'] === 'a') include 'popupForm.php'; ?>
    <div class="div2"> <?php include('todays_task.php'); ?> </div>
    <div class="div3"> <?php include('team_members.php'); ?> </div>
</div>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>
