<?php
session_start();
include('../../conf/database/db_connect.php');
include('../../examples/includes/header.php');

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action'])) {
        $taskId = $data['id'] ?? null;

        if ($data['action'] === 'update' && $taskId) {
            $newStatus = $data['status'] ?? null;

            if ($newStatus === 'done') {
                // Check duration and update leaderboard
                $query = "SELECT task_start_date, task_duration 
                          FROM KaajAsse.task_calendar 
                          WHERE task_id = '$taskId'";
                $result = mysqli_query($connect, $query);
                $task = mysqli_fetch_assoc($result);

                if ($task) {
                    $startDate = new DateTime($task['task_start_date']);
                    $currentDate = new DateTime();
                    $duration = $task['task_duration'];

                    $daysTaken = $startDate->diff($currentDate)->days;

                    // Fetch all users assigned to this task
                    $usersQuery = "SELECT user_id FROM KaajAsse.task_user WHERE task_id = '$taskId'";
                    $usersResult = mysqli_query($connect, $usersQuery);

                    while ($user = mysqli_fetch_assoc($usersResult)) {
                        $userId = $user['user_id'];
                        $points = ($daysTaken > $duration) ? -5 : 5;

                        // Update points in leaderboard
                        $updatePointsQuery = "UPDATE KaajAsse.task_leaderboard 
                                              SET points = points + $points 
                                              WHERE user_id = '$userId'";
                        mysqli_query($connect, $updatePointsQuery);
                    }
                }
            }

            // Update task status
            $updateQuery = "UPDATE KaajAsse.task_calendar SET task_status = '$newStatus' WHERE task_id = '$taskId'";
            mysqli_query($connect, $updateQuery);
            exit;
        }

        if ($data['action'] === 'delete' && $taskId) {
            // Delete task
            $deleteQuery = "DELETE FROM KaajAsse.task_calendar WHERE task_id = '$taskId'";
            mysqli_query($connect, $deleteQuery);
            echo json_encode(['message' => 'Task deleted successfully.']);
            exit;
        }
    }
}

// Render page for GET request (Normal page load)
if (isset($_SESSION['user_id'], $_SESSION['uname'])) {
    $user_id = mysqli_real_escape_string($connect, $_SESSION['user_id']);

    // Fetch tasks assigned to the logged-in user
    $query = "SELECT tc.* 
              FROM KaajAsse.task_calendar tc
              JOIN KaajAsse.task_user tu ON tc.task_id = tu.task_id
              WHERE tu.user_id = $user_id";
    $result = mysqli_query($connect, $query);

    $tasks = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tasks[$row['task_status']][] = $row;
    }
}
?>

<h1 style="margin-bottom: 10px;">Kanban Board</h1>
<hr style="margin-bottom: 20px;">
<div class="kanban-board">
    <?php
    $statuses = ['backlog', 'todo', 'inprogress', 'done'];
    foreach ($statuses as $status) {
        echo "<div class='kanban-column' data-status='$status'>";
        echo "<h2 class='column-title $status'>" . ucfirst($status) . "</h2>";

        if (!empty($tasks[$status])) {
            foreach ($tasks[$status] as $task) {
                $isDraggable = $status !== 'done' ? 'true' : 'false';
                echo "<div class='task' draggable='$isDraggable' data-id='{$task['task_id']}'>";
                echo "<p class='priority {$task['task_priority']}'>{$task['task_priority']} Priority</p>";
                echo "<p class='task-title'>{$task['task_name']}</p>";
                echo "<p class='date'> Start Date: {$task['task_start_date']}</p>";
                echo "<p> Duration: {$task['task_duration']} day(s)</p>";

                if ($status === 'done') {
                    echo "<button class='delete-task' data-id='{$task['task_id']}'>🗑️</button>";
                }

                echo "</div>";
            }
        }

        echo "</div>";
    }
    ?>
</div>

<script>
const tasks = document.querySelectorAll('.task');
const columns = document.querySelectorAll('.kanban-column');

// Handle dragging
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

// Handle dropping
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
                body: JSON.stringify({ action: 'update', id: taskId, status: newStatus })
            })
            .then(()=>{
            if (newStatus === 'done') {
                    const deleteBtn = document.createElement('button');
                    deleteBtn.textContent = '🗑️';
                    deleteBtn.classList.add('delete-task');
                    deleteBtn.dataset.id = taskId;
                    task.appendChild(deleteBtn);
                    task.setAttribute('draggable', 'false');
                }
        })
            .catch(error => {
                console.error(error);
            });
        }
        column.style.backgroundColor = '';
    });
});

// Handle task deletion
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('delete-task')) {
        const taskId = e.target.dataset.id;

        // Remove the task from the DOM immediately
        const taskElement = document.querySelector(`.task[data-id="${taskId}"]`);
        if (taskElement) taskElement.remove();

        // Send the delete request to the server
        fetch(window.location.href, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'delete', id: taskId })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data.message); // Optional: Log the server's success message
        })
        .catch(error => {
            console.error('Error deleting task:', error);
            // If an error occurs, re-add the task back to the DOM
            if (taskElement) {
                const column = document.querySelector(`.kanban-column[data-status="done"]`);
                if (column) column.appendChild(taskElement);
            }
        });
    }
});
</script>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>
