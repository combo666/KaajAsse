<div id="popupForm" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <form id="taskForm">
            <label for="task_name">Task Name:</label>
            <input type="text" id="task_name" name="task_name" required>

            <label for="task_date">Task Date:</label>
            <input type="text" id="task_date" name="task_date" readonly>

            <label for="task_duration">Duration (days):</label>
            <input type="number" id="task_duration" name="task_duration" min="1" required>

            <label for="assigned_user">Assigned User:</label>
            <input type="text" id="assigned_user" name="assigned_user" required>

            <label for="task_description">Description:</label>
            <textarea id="task_description" name="task_description" rows="4" required></textarea>

            <button type="submit">Save Task</button>
        </form>

    </div>
</div>