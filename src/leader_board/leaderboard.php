<?php
session_start();
include('../../conf/database/db_connect.php');
include('../../examples/includes/header.php');

if (isset($_SESSION['user_id'], $_SESSION['uname'])) {
    $sql = "SELECT u.user_id, u.first_name, u.last_name, t.points
            FROM KaajAsse.user u
            JOIN KaajAsse.task_leaderboard t ON u.user_id = t.user_id
            ORDER BY t.points DESC";

    $result = mysqli_query($connect, $sql);

    if (!$result) {
        die('<p class="error">Error executing query: ' . htmlspecialchars(mysqli_error($connect)) . '</p>');
    }
    ?>

    <div class="leaderboard-title">Leaderboard</div>
    <div class="leaderboard-header">
        <div class="header-name">Name</div>
        <div class="header-points">Points</div>
        <div class="header-rank">Rank</div>
    </div>

    <?php
    if (mysqli_num_rows($result) > 0) {
        $rank = 1; 
        $previousPoints = null; 
        $actualRank = 1; 

        while ($row = mysqli_fetch_assoc($result)) {
            $userAvatar = strtoupper(substr($row['first_name'], 0, 1));
            $userName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
            $userPoints = (int) $row['points']; 

            if ($previousPoints !== null && $userPoints < $previousPoints) {
                $rank++;
                $actualRank = $rank; 
                
            }

            ?>
            <div class="leaderboard-item">
                <div class="user-info">
                    <div class="user-avatar"><?php echo $userAvatar; ?></div>
                    <div class="user-name"><?php echo $userName; ?></div>
                </div>
                <div class="user-points"><?php echo $userPoints; ?></div>
                <div class="user-rank">Rank: <?php echo $actualRank; ?></div> 
            </div>
            <?php
            $previousPoints = $userPoints;
        }
    } else {
        echo "<p>No leaderboard data available.</p>";
    }
} else {
    echo "<p class='error'>You must be logged in to view the leaderboard.</p>";
}
?>

<?php include('../../examples/includes/navbar.php'); ?>
<?php include('../../examples/includes/footer.php'); ?>
