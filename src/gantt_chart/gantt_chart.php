<?php
include('../../examples/includes/header.php');
include('../../conf/database/db_connect.php');
session_start();

echo $_SESSION['user_id']
    ?>
<div class="gantt-header">
    <h1>📊 Gantt Chart</h1>
    <button class="control-btn">Add Task</button>
</div>

<div class="gantt-chart">
    <!-- Task Row -->
    <div class="gantt-row">
        <div class="task-label">Blog App Development</div>
        <div class="task-bar" style="left: 10%; width: 20%; background-color: #f4b3b3;">
            <span>Design Phase</span>
        </div>
    </div>

    <div class="gantt-row">
        <div class="task-label">Admin Dashboard</div>
        <div class="task-bar" style="left: 30%; width: 25%; background-color: #b3c7f3;">
            <span>Development</span>
        </div>
    </div>

    <div class="gantt-row">
        <div class="task-label">API Integration</div>
        <div class="task-bar" style="left: 60%; width: 15%; background-color: #b4f3b3;">
            <span>Testing</span>
        </div>
    </div>

    <div class="gantt-row">
        <div class="task-label">Final Deployment</div>
        <div class="task-bar" style="left: 80%; width: 10%; background-color: #f3e7b3;">
            <span>Deployment</span>
        </div>
    </div>
</div>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>