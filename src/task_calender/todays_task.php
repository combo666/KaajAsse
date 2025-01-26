<div class="task_container">
    <div class="task_header">
        My Tasks for Today
    </div>
    <div class="tasks_bg">
        <?php
        // Include the database connection file
        include('../../conf/database/db_connect.php');

        // Ensure the session is started
        if (!isset($_SESSION)) {
            session_start();
        }

        // Get the current user's email from the session
        $current_user_email = $_SESSION['user_email'] ?? null;

        if (!$current_user_email) {
            echo '<p>Error: User is not logged in.</p>';
            exit;
        }

        // SQL query to fetch tasks assigned to the logged-in user for today
        $tasks_query = "
            SELECT 
                tc.task_name, 
                tc.task_start_date AS task_date, 
                tc.task_description AS task_note, 
                tc.task_color AS t_color, 
                tc.task_priority AS priority, 
                tc.task_id
            FROM KaajAsse.task_user tu
            INNER JOIN KaajAsse.task_calendar tc ON tu.task_id = tc.task_id
            INNER JOIN KaajAsse.user u ON tu.user_id = u.user_id
            WHERE u.user_email = ?
            AND tc.task_start_date = CURDATE()
            ORDER BY FIELD(tc.task_priority, 'high', 'medium', 'low')";

        // Prepare the SQL statement
        $stmt = $connect->prepare($tasks_query);
        if (!$stmt) {
            echo '<p>Error preparing the query: ' . htmlspecialchars($connect->error) . '</p>';
            exit;
        }

        // Bind the email parameter
        $stmt->bind_param('s', $current_user_email);
        

        // Execute the query
        if (!$stmt->execute()) {
            echo '<p>Error executing the query: ' . htmlspecialchars($stmt->error) . '</p>';
            exit;
        }

        // Fetch the result
        $result = $stmt->get_result();

        // Priority color mapping
        $priority_colors = [
            'high' => '#FF2B14', // Red for high priority
            'medium' => '#A4D2DF', // Yellow for medium priority
            'low' => '#D4F5A4' // Green for low priority
        ];

        // Check if any tasks are available for today
        if ($result->num_rows > 0) {
            while ($task = $result->fetch_assoc()) {
                $priority = $task['priority'];
                $priority_color = $priority_colors[$priority] ?? '#ffffff'; // Default white for undefined 
                
                

                // Display each task in a card
                echo '
                    <div class="task_card" onclick="window.location=\'task_calender.php?task_id=' . $task['task_id'] . '\'">
                        <div style="display: flex; align-items: center; padding: 0px;">
                            <span class="dot_priority" style="background-color: ' . htmlspecialchars($task['t_color'] ?: "#ffffff") . ';"></span>
                            <p class="date">' . htmlspecialchars($task['task_date']) . '</p>
                        </div>
                        <p class="task_name">' . htmlspecialchars($task['task_name']) . '</p>
                        <p class="task_note">' . htmlspecialchars($task['task_note']) . '</p>
                        Priority:
                        <p class="task_priority" style="color: ' . htmlspecialchars($priority_color) . '; font-weight: bold;">
                             ' . ucfirst($priority) . '
                        </p>
                    </div>
                ';
            }
        } else {
            // No tasks available for today
            echo '<p>No tasks assigned to you for today.</p>';
        }

        // Close the statement
        $stmt->close();
        ?>
    </div>
</div>
