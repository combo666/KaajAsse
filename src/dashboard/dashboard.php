<?php
session_start();
include '../../examples/includes/header.php';
include('../../examples/includes/navbar.php');

if (!isset($_SESSION['uname'])) {
    header('Location: ../login/login_reg.php');
    exit();
}

// Database connection
include('../../conf/database/db_connect.php');

// Fetch task data by status
$totalTasks = 0;
$backlogTasks = 0;
$todoTasks = 0;
$inProgressTasks = 0;
$doneTasks = 0;

// Fetch total tasks
$sqlTotalTasks = "SELECT COUNT(*) AS total FROM task_calendar";
$resultTotal = $connect->query($sqlTotalTasks);
if ($resultTotal->num_rows > 0) {
    $totalTasks = $resultTotal->fetch_assoc()['total'];
}

// Fetch task counts by status
$sqlStatusCounts = "
    SELECT task_status, COUNT(*) AS count 
    FROM task_calendar 
    GROUP BY task_status
";
$resultStatusCounts = $connect->query($sqlStatusCounts);
if ($resultStatusCounts->num_rows > 0) {
    while ($row = $resultStatusCounts->fetch_assoc()) {
        switch ($row['task_status']) {
            case 'backlog':
                $backlogTasks = $row['count'];
                break;
            case 'todo':
                $todoTasks = $row['count'];
                break;
            case 'inprogress':
                $inProgressTasks = $row['count'];
                break;
            case 'done':
                $doneTasks = $row['count'];
                break;
        }
    }
}
?>

<div class="main-container">
    <!-- Cards Section -->
    <div class="card-container">
        <div class="card">
            <h4>Total Tasks</h4>
            <div class="card-content">
                <h2><?php echo $totalTasks; ?></h2>
                <p>All tasks in the system</p>
            </div>
        </div>

        <div class="card">
            <h4>Backlog</h4>
            <div class="card-content">
                <h2><?php echo $backlogTasks; ?></h2>
                <p>Tasks yet to be planned</p>
            </div>
        </div>

        <div class="card">
            <h4>To Do</h4>
            <div class="card-content">
                <h2><?php echo $todoTasks; ?></h2>
                <p>Planned but not started</p>
            </div>
        </div>

        <div class="card">
            <h4>In Progress</h4>
            <div class="card-content">
                <h2><?php echo $inProgressTasks; ?></h2>
                <p>Currently being worked on</p>
            </div>
        </div>

        <div class="card">
            <h4>Done</h4>
            <div class="card-content">
                <h2><?php echo $doneTasks; ?></h2>
                <p>Tasks that are completed</p>
            </div>
        </div>
    </div>

    <!-- Pie Chart Section -->
    <div class="pie-chart-container">
        <h2>Task Distribution</h2>
        <canvas id="taskPieChart"></canvas>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('taskPieChart').getContext('2d');
let delayed;

const taskPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Backlog', 'To Do', 'In Progress', 'Done'],
        datasets: [{
            data: [
                <?php echo $backlogTasks; ?>, 
                <?php echo $todoTasks; ?>, 
                <?php echo $inProgressTasks; ?>, 
                <?php echo $doneTasks; ?>
            ],
            backgroundColor: [
                'rgba(255, 152, 0, 0.8)',
                'rgba(255, 193, 7, 0.8)',
                'rgba(3, 169, 244, 0.8)',
                'rgba(76, 175, 80, 0.8)'
            ],
            hoverBackgroundColor: [
                'rgba(255, 152, 0, 1)',
                'rgba(255, 193, 7, 1)',
                'rgba(3, 169, 244, 1)',
                'rgba(76, 175, 80, 1)'
            ],
            borderWidth: 2,
            borderColor: '#FFF',
            hoverBorderWidth: 3,
            hoverOffset: 15,
            transition: 'all 0.5s ease'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: {
            animateScale: true,
            animateRotate: true,
            duration: 2000,
            easing: 'easeInOutQuart',
            onComplete: () => {
                delayed = true;
            },
            delay: (context) => {
                let delay = 0;
                if (!delayed) {
                    delay = context.dataIndex * 500;
                }
                return delay;
            }
        },
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    font: {
                        size: 14,
                        family: 'Arial'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            },
            tooltip: {
                animation: {
                    duration: 400,
                    easing: 'easeOutQuart'
                },
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                titleColor: '#333',
                bodyColor: '#666',
                borderColor: '#ddd',
                borderWidth: 1,
                padding: 15,
                cornerRadius: 8,
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.parsed || 0;
                        return `${label}: ${value} tasks`;
                    }
                }
            }
        }
    }
});
</script>


<style>
.main-container {
    width: 1200px;
    margin: 0 auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.card-container {
    width: 100%;
    display: flex;
    flex-wrap: nowrap; /* Prevent wrapping */
    gap: 15px;
    justify-content: center;
    padding: 10px;
    overflow-x: auto; /* Allow horizontal scroll if needed */
}

.card {
    width: 220px; /* Slightly smaller to fit all 5 */
    min-width: 220px; /* Prevent shrinking */
    height: 150px;
    background:rgba(250, 220, 130, 0.77);
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.2s ease;
    flex: 0 0 auto; /* Prevent growing/shrinking */
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.2);
}

.card h4 {
    color: #333;
    font-size: 1.2em;
    margin-bottom: 15px;
}

.card-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: calc(100% - 50px);
}

.card h2 {
    font-size: 2em;
    color: #333;
    margin: 10px 0;
}

.card p {
    color: #666;
    font-size: 1em;
    margin: 5px 0;
}

.pie-chart-container {
    width: 400px;
    margin: 40px auto;
    padding: 20px;
    background: #FFF9E7;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    text-align: center;
}

.pie-chart-container h2 {
    color: #333;
    margin-bottom: 20px;
}

#taskPieChart {
    max-width: 100%;
    height: 300px !important;
}

@media (max-width: 1200px) {
    .main-container {
        width: 95%;
    }
}
.log-container {
    width: 80%;
    margin: 20px auto;
    padding: 20px;
    background: #FFF9E7;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.log-container h2 {
    color: #333;
    margin-bottom: 15px;
    font-size: 1.2em;
}

.log-list {
    max-height: 300px;
    overflow-y: auto;
    padding: 10px;
}

.log-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 15px;
}

.log-time {
    color: #666;
    font-size: 0.9em;
    min-width: 100px;
}

.log-action {
    color: #F15E29;
    font-weight: bold;
}

.log-details {
    color: #333;
    flex: 1;
}
</style>

<?php include '../../examples/includes/footer.php'; ?>
