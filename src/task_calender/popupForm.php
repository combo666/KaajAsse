<div id="popupForm" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <form id="taskForm" method="post" action="assign_task.php">
            <!-- Existing or New Task -->
            <label for="task_option">Task:</label>
            <select id="task_option" name="task_option" required onchange="toggleTaskInput()">
                <option value="existing" selected>Existing Task</option>
                <option value="new">New Task</option>
            </select>

            <!-- Existing Task Dropdown -->
            <div id="existingTaskDropdown">
                <label for="task_name">Select Task:</label>
                <select id="task_name" name="task_name">
                    <option value="" disabled selected>Select Task</option>
                    <?php
                    $tasks_query = "SELECT task_id, task_name FROM KaajAsse.task_calendar";
                    $tasks_result = $connect->query($tasks_query);
                    while ($task = $tasks_result->fetch_assoc()) {
                        echo '<option value="' . $task['task_id'] . '">' . htmlspecialchars($task['task_name']) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <!-- New Task Input -->
            <div id="newTaskInput" style="display:none;">
                <label for="new_task_name">New Task Name:</label>
                <input type="text" id="new_task_name" name="new_task_name">
            </div>

            <label for="task_date">Task Date:</label>
            <input type="date" id="task_date" name="task_date" required>

            <label for="task_duration">Duration (days):</label>
            <input type="number" id="task_duration" name="task_duration" min="1" required>

            <label for="assigned_user">Assigned User:</label>
            <select id="assigned_user" name="assigned_user[]" multiple>
                <option value="" disabled>Select User(s)</option>
                <?php
                $users_query = "SELECT user_id, CONCAT(first_name, ' ', last_name) AS full_name FROM KaajAsse.user";
                $users_result = $connect->query($users_query);
                while ($user = $users_result->fetch_assoc()) {
                    echo '<option value="' . $user['user_id'] . '">' . '(' . htmlspecialchars($user['user_id']) . ') ' .  htmlspecialchars($user['full_name']) . '</option>';
                }
                ?>
            </select>

            <label for="task_description">Description:</label>
            <textarea id="task_description" name="task_description" rows="4"></textarea>

            <label for="task_color">Task Color:</label>
            <input type="color" id="task_color" name="task_color" value="#cab64e">

            <button type="submit">Save Task</button>
        </form>
    </div>
</div>

<script>
    function toggleTaskInput() {
        const taskOption = document.getElementById('task_option').value;
        const existingDropdown = document.getElementById('existingTaskDropdown');
        const newTaskInput = document.getElementById('newTaskInput');
        if (taskOption === 'existing') {
            existingDropdown.style.display = 'block';
            newTaskInput.style.display = 'none';
        } else {
            existingDropdown.style.display = 'none';
            newTaskInput.style.display = 'block';
        }
    }
</script>
