<div class="task_container">
    <div class="task_header">
        Today's Tasks
    </div>
    <div class="tasks_bg">
        <?php
        include('../../conf/database/db_connect.php');

        // Ensure session is started
        if (!isset($_SESSION)) {
            session_start();
        }

        // Fetch tasks for the current user
        $current_user_email = $_SESSION['uname']; // Assuming 'uname' stores the current user's email

        // Query to fetch tasks for the logged-in user
        $tasks_query = "
            SELECT 
                tc.task_name, 
                tc.task_start_date AS task_date, 
                tc.task_description AS task_note, 
                tc.task_color 
            FROM KaajAsse.task_user tu
            INNER JOIN KaajAsse.task_calendar tc ON tu.task_id = tc.task_id
            INNER JOIN KaajAsse.user u ON tu.user_id = u.user_id
            WHERE u.user_email = ?
            AND CURDATE() BETWEEN tc.task_start_date 
            AND DATE_ADD(tc.task_start_date, INTERVAL tc.task_duration - 1 DAY)";

        $stmt = $connect->prepare($tasks_query);
        if (!$stmt) {
            echo '<p>Error preparing the query: ' . $connect->error . '</p>';
            exit;
        }

        // Bind the email parameter
        $stmt->bind_param('s', $current_user_email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Display tasks
        if ($result->num_rows > 0) {
            while ($task = $result->fetch_assoc()) {
                echo '
                    <div class="task_card">
                        <div style="display: flex; align-items: center; padding: 0px;">
                            <span class="dot_priority" style="background-color: ' . htmlspecialchars($task['task_color']) . ';"></span>
                            <p class="date">' . htmlspecialchars($task['task_date']) . '</p>
                        </div>
                        <p class="task_name">' . htmlspecialchars($task['task_name']) . '</p>
                        <p class="task_note">' . htmlspecialchars($task['task_note']) . '</p>
                    </div>
                ';
            }
        } else {
            echo '<p>No tasks for today!</p>';
        }

        $stmt->close();
        ?>
    </div>
</div>
