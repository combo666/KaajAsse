<?php
session_start();
include('../../conf/database/db_connect.php');

// Handle AJAX request to update the task status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $taskId = $data['id'] ?? null;
    $newStatus = $data['status'] ?? null;

    if ($taskId && $newStatus) {
        // Simulate database update (replace this with real DB logic)
        echo "Task ID $taskId status changed to '$newStatus'.";
    } else {
        echo "Error: Invalid task ID or status.";
    }
    exit;  // Stop script execution
}

// Render page for GET request (Normal page load)
include('../../examples/includes/header.php');
?>

<div class="controls">
    <button class="control-btn">✏️ Create Task</button>
    <button class="control-btn">📂 Filter</button>
    <button class="control-btn">List View</button>
    <button class="control-btn trash-btn">🗑️ Trashed Tasks</button>
</div>

<div class="kanban-board">
    <div class="kanban-column" data-status="backlog">
        <h2 class="column-title">Backlog</h2>
        <div class="task" draggable="true" data-id="1">
            <p class="priority">Medium Priority</p>
            <p class="task-title">Fix UI bugs</p>
        </div>
    </div>

    <div class="kanban-column" data-status="todo">
        <h2 class="column-title">To Do</h2>
        <div class="task" draggable="true" data-id="2">
            <p class="priority">High Priority</p>
            <p class="task-title">Implement API</p>
        </div>
    </div>

    <div class="kanban-column" data-status="inprogress">
        <h2 class="column-title">In Progress</h2>
        <div class="task" draggable="true" data-id="3">
            <p class="priority">Low Priority</p>
            <p class="task-title">Write documentation</p>
        </div>
    </div>

    <div class="kanban-column" data-status="done">
        <h2 class="column-title">Done</h2>
        <div class="task" draggable="true" data-id="4">
            <p class="priority">Completed</p>
            <p class="task-title">Deploy application</p>
        </div>
    </div>
</div>

<p id="status-message"></p>

<script>
const tasks = document.querySelectorAll('.task');
const columns = document.querySelectorAll('.kanban-column');
const statusMessage = document.getElementById('status-message');

// Allow dragging
tasks.forEach(task => {
    task.addEventListener('dragstart', (e) => {
        e.dataTransfer.setData('text/plain', e.target.dataset.id);
        e.dataTransfer.setData('parent-status', e.target.closest('.kanban-column').dataset.status);
        e.target.classList.add('dragging');
    });

    task.addEventListener('dragend', (e) => {
        e.target.classList.remove('dragging');
    });
});

// Allow dropping into columns
columns.forEach(column => {
    column.addEventListener('dragover', (e) => {
        e.preventDefault();
        column.style.backgroundColor = '#e0f7fa';
    });

    column.addEventListener('dragleave', () => {
        column.style.backgroundColor = '';
    });

    column.addEventListener('drop', (e) => {
        e.preventDefault();
        const taskId = e.dataTransfer.getData('text/plain');
        const previousStatus = e.dataTransfer.getData('parent-status');
        const newStatus = column.dataset.status;

        if (previousStatus !== newStatus) {
            const task = document.querySelector(`.task[data-id="${taskId}"]`);
            column.appendChild(task);

            fetch(window.location.href, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: taskId, status: newStatus })
            })
            .then(response => response.text())
            .then(message => {
                statusMessage.textContent = message;
            })
            .catch(error => {
                statusMessage.textContent = 'Error updating status.';
                console.error(error);
            });
        }
        column.style.backgroundColor = '';
    });
});
</script>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>
