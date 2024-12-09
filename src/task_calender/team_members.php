<?php
// Check if task_id is set
$task_id = $_GET['task_id'] ?? null;

include('../../conf/database/db_connect.php');

// Fetch task name using task_id
$task_name_query = "SELECT task_name FROM KaajAsse.task_calendar WHERE task_id = ?";
$task_name_stmt = $connect->prepare($task_name_query);
if (!$task_name_stmt) {
    echo 'Error preparing task name query: ' . htmlspecialchars($connect->error);
    exit;
}

$task_name_stmt->bind_param('i', $task_id);
$task_name_stmt->execute();
$task_name_result = $task_name_stmt->get_result();
$task_name = '';

if ($task_name_result->num_rows > 0) {
    $task_name_row = $task_name_result->fetch_assoc();
    $task_name = $task_name_row['task_name'];
}

$task_name_stmt->close();

// SQL query to fetch assigned members for a specific task
$members_query = "
    SELECT 
        u.user_id,
        u.first_name,
        u.last_name,
        u.user_email,
        u.user_role,
        u.profile_image
    FROM KaajAsse.task_user tu
    INNER JOIN KaajAsse.user u ON tu.user_id = u.user_id
    WHERE tu.task_id = ?";

// Prepare the statement
$stmt = $connect->prepare($members_query);
if (!$stmt) {
    echo 'Error preparing the query: ' . htmlspecialchars($connect->error);
    exit;
}

$stmt->bind_param('i', $task_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if members are assigned to the task
if ($result->num_rows > 0) {
    $assignedMembers = [];
    while ($member = $result->fetch_assoc()) {
        $assignedMembers[] = $member;
    }
} else {
    $assignedMembers = [];
}

// Close the statement
$stmt->close();
?>


<div class="teamMembersParent">
    <div class="teamMembersHead">
        <div class="teamMembersTitle">Task Name: <?php echo htmlspecialchars($task_name) ?: 'No task selected!'; ?></div>
        <div class="teamMembersSubtitle">
            Assigned: <?php echo count($assignedMembers); ?> person
        </div>
        <hr>
    </div>
    <div class="assigned">
        <?php
        // Loop through the assigned members and display them
        foreach ($assignedMembers as $member) {
            echo '
                <div class="assignedMembers">
                    <div class="assignedProImage">
                        <img class="assignedProImg" src="' . (empty($member['profile_image']) ? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQABqQIdskCD9BK0I81EbVfV9tTz320XvJ35A&s' : htmlspecialchars($member['profile_image'])) . '" alt="">
                    </div>
                    <div class="assignedContent">
                        <div class="assignedName">Name:'. '   ' . htmlspecialchars($member['first_name']) . ' ' . htmlspecialchars($member['last_name']) . '</div>
                        <div class="assignedEmail">Email:'. '   '  . htmlspecialchars($member['user_email']) . '</div>
                        <div class="assignedPosition">Position:'. '   '  . htmlspecialchars($member['user_role'] == 'a' ? "Admin" : "Member") . '</div>
                    </div>
                </div>
            ';
        }
        ?>
    </div>
</div>

