<?php
include('../../examples/includes/header.php'); 
include('../../conf/database/db_connect.php');
session_start();

echo $_SESSION['user_id']
?>

    <div class="controls">
        <button class="control-btn">✏️ Create Task</button>
        <button class="control-btn">📂 Filter</button>
        <button class="control-btn">List View</button>
        <button class="control-btn trash-btn">🗑️ Trashed Tasks</button>
    </div>

    <div class="kanban-board">
        <div class="kanban-column">
            <h2 class="column-title backlog">Backlog</h2>
            <div class="task medium-priority">
                <p class="priority">Medium Priority</p>
                <p class="task-title">Duplicate-Duplicate review code</p>
                <p class="project">Blog App Admin Dashboard</p>
                <p class="date">8-Feb-2024</p>
                <button class="subtask-btn">Add Subtask</button>
            </div>
            <div class="task low-priority">
                <p class="priority">Low Priority</p>
                <p class="task-title">Duplicate-Duplicate review code</p>
                <p class="project">Blog App Admin Dashboard</p>
                <p class="date">8-Feb-2024</p>
                <button class="subtask-btn">Add Subtask</button>
            </div>
        </div>
        <div class="kanban-column">
            <h2 class="column-title todo">To Do</h2>
        </div>
        <div class="kanban-column">
            <h2 class="column-title in-progress">In Progress</h2>
        </div>
        <div class="kanban-column">
            <h2 class="column-title done">Done</h2>
        </div>
    </div>






<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>