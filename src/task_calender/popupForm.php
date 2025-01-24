<div id="popupForm" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <form id="taskForm" method="post" action="../save_task.php">
            <!-- Existing or New Task -->
            <label for="task_option">Task:</label>
            <select id="task_option" name="task_option" required onchange="toggleTaskInput()">
                <option value="existing" selected>Existing Task</option>
                <option value="new">New Task</option>
            </select>

            <!-- Existing Task Dropdown -->
            <div id="existingTaskDropdown" onmouseover="showDropdown(event)" onmouseleave="hideDropdown(event)">
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

            <div id="existingTaskDetails" style="display:none;">
                <h4>Task Details</h4>
                <p id="taskNameDisplay"></p>
                <p id="taskDateDisplay"></p>
                <p id="taskDurationDisplay"></p>
                <p id="taskDescriptionDisplay"></p>
            </div>



            <!-- New Task Input -->
            <div id="newTaskInput" style="display:none;">
                <label for="new_task_name">New Task Name:</label>
                <input type="text" id="new_task_name" name="new_task_name">
            </div>

            <div id="taskDetails">
                <label for="task_date">Task Date:</label>
                <input type="date" id="task_date" name="task_date" required>

                <label for="task_duration">Duration (days):</label>
                <input type="number" id="task_duration" name="task_duration" min="1" required>

                <label for="task_description">Description:</label>
                <textarea id="task_description" name="task_description" rows="4"></textarea>

                <label for="task_color">Task Color:</label>
                <input type="color" id="task_color" name="task_color" value="#cab64e">
            </div>

            <label for="assigned_user">Assigned User:</label>
            <select id="assigned_user" name="assigned_user[]" multiple>
                <option value="" disabled>Select User(s)</option>
                <?php
                // Fetch users from the database
                $users_query = "SELECT user_id, CONCAT(first_name, ' ', last_name) AS full_name FROM KaajAsse.user";
                $users_result = $connect->query($users_query);
                while ($user = $users_result->fetch_assoc()) {
                    echo '<option value="' . $user['user_id'] . '">' . '(' . htmlspecialchars($user['user_id']) . ') ' .  htmlspecialchars($user['full_name']) . '</option>';
                }
                ?>
            </select>


            <button type="submit">Save Task</button>
        </form>
    </div>
</div>

<script>
    // Function to toggle visibility of form elements
    function toggleTaskInput() {
        const taskOption = document.getElementById('task_option').value;
        const existingDropdown = document.getElementById('existingTaskDropdown');
        const newTaskInput = document.getElementById('newTaskInput');
        const taskDetails = document.getElementById('taskDetails');
        const existingTaskDetails = document.getElementById('existingTaskDetails');

        const taskDetailInputs = taskDetails.querySelectorAll('input, textarea');

        if (taskOption === 'existing') {
            existingDropdown.style.display = 'block';
            newTaskInput.style.display = 'none';
            taskDetails.style.display = 'none';
            existingTaskDetails.style.display = 'block';

            // Disable task details inputs
            taskDetailInputs.forEach(input => input.disabled = true);

            // Fetch existing task details when a task is selected
            document.getElementById('task_name').addEventListener('change', fetchExistingTaskDetails);
        } else {
            existingDropdown.style.display = 'none';
            newTaskInput.style.display = 'block';
            taskDetails.style.display = 'block';
            existingTaskDetails.style.display = 'none';

            // Enable task details inputs
            taskDetailInputs.forEach(input => input.disabled = false);
        }
    }

    function fetchExistingTaskDetails() {
        const taskId = document.getElementById('task_name').value;

        if (taskId) {
            fetch(`fetch_task_details.php?task_id=${taskId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Populate the task details
                        document.getElementById('taskNameDisplay').textContent = `Name: ${data.task_name}`;
                        document.getElementById('taskDateDisplay').textContent = `Start Date: ${data.task_start_date}`;
                        document.getElementById('taskDurationDisplay').textContent = `Duration: ${data.task_duration} days`;
                        document.getElementById('taskDescriptionDisplay').textContent = `Description: ${data.task_description}`;
                    } else {
                        alert(data.message || 'Failed to fetch task details.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching task details:', error);
                });
        }
    }

    // Ensure correct elements are shown/hidden on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleTaskInput();

        // Prevent form resubmission
        const form = document.getElementById('taskForm');
        form.addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent default submission behavior

            // Disable submit button to avoid duplicate requests
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton.disabled) {
                return; // Prevent multiple submissions
            }
            submitButton.disabled = true;

            const formData = new FormData(form);

            // Send the data using Fetch API
            fetch('./save_task.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Reload to reflect changes
                    } else {
                        alert(data.message || 'Failed to save the task.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the task.');
                })
                .finally(() => {
                    submitButton.disabled = false; // Re-enable the button after the request is finished
                });
        });
    });
</script>
