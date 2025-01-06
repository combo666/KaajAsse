<?php
session_start();
include('../../conf/database/db_connect.php');
include('../../examples/includes/header.php');

// Handle AJAX request to update the task status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $taskId = $data['id'] ?? null;
    $newStatus = $data['status'] ?? null;

    if ($taskId && $newStatus) {
        $updateQuery = "UPDATE KaajAsse.task_calendar SET task_status = '$newStatus' WHERE task_id = '$taskId'";
        mysqli_query($connect, $updateQuery);
        // echo "Task ID $taskId status changed to '$newStatus'.";
    }
    exit;
}

// Render page for GET request (Normal page load)
if (isset($_SESSION['user_id'], $_SESSION['uname'])) {
    $user_id = mysqli_real_escape_string($connect, $_SESSION['user_id']);
    $query = "SELECT * FROM KaajAsse.task_calendar WHERE assigned_user = $user_id";
    $result = mysqli_query($connect, $query);

    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[$row['task_status']][] = $row;
    }
}
?>

<div class="controls">
    <button class="control-btn">✏️ Create Task</button>
    <button class="control-btn trash-btn">🗑️ Trashed Tasks</button>
</div>

<div class="kanban-board">
    <?php
    $statuses = ['backlog', 'todo', 'inprogress', 'done'];
    foreach ($statuses as $status) {
        echo "<div class='kanban-column' data-status='$status'>";
        echo "<h2 class='column-title $status'>" . ucfirst($status) . "</h2>";

        if (!empty($tasks[$status])) {
            foreach ($tasks[$status] as $task) {
                echo "<div class='task' draggable='true' data-id='{$task['task_id']}'>";
                echo "<p class='priority'>{$task['task_priority']} Priority</p>";
                echo "<p class='task-title'>{$task['task_name']}</p>";
                echo "<p class='date'>{$task['task_start_date']}</p>";
                echo "</div>";
            }
        }

        echo "</div>";
    }
    ?>
</div>

<!-- <p id="status-message"></p> -->

<script>
const tasks = document.querySelectorAll('.task');
const columns = document.querySelectorAll('.kanban-column');
// const statusMessage = document.getElementById('status-message');

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
            // .then(response => response.text())
            // .then(message => {
            //     statusMessage.textContent = message;
            // })
            .catch(error => {
                // statusMessage.textContent = 'Error updating status.';
                console.error(error);
            });
        }
        column.style.backgroundColor = '';
    });
});
</script>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>
