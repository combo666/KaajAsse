<?php
session_start();
include '../../examples/includes/header.php';
include('../../examples/includes/navbar.php');

if (!isset($_SESSION['uname'])) {
    header('Location: ../login/login_reg.php');
    exit();
}
?>

<div class="main-container">
    <div class="card-container">
        <div class="card">
            <h4>Total Tasks</h4>
            <div class="card-content">
                <h2>486</h2>
                <p>Completed: 301</p>
            </div>
        </div>

        <div class="card">
            <h4>In Progress</h4>
            <div class="card-content">
                <h2>58</h2>
                <p>Active: 47</p>
            </div>
        </div>

        <div class="card">
            <h4>Pending</h4>
            <div class="card-content">
                <h2>12</h2>
                <p>Waiting: 8</p>
            </div>
        </div>

        <div class="card">
            <h4>Completed</h4>
            <div class="card-content">
                <h2>144</h2>
                <p>Done: 80</p>
            </div>
        </div>
    </div>

    <div class="pie-chart-container">
        <h2>Task Distribution</h2>
        <canvas id="taskPieChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('taskPieChart').getContext('2d');
const taskPieChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Completed', 'In Progress', 'Pending'],
        datasets: [{
            data: [301, 47, 8],
            backgroundColor: ['#4CAF50', '#FFC107', '#F44336'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
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
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 20px;
}

.card {
    width: 250px;
    height: 150px;
    background: #FFF9E7;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    padding: 20px;
    text-align: center;
    transition: transform 0.2s ease;
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
</style>

<?php include '../../examples/includes/footer.php'; ?>