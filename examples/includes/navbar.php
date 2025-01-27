<?php
// Get the current page filename (without the path)
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="navbar">
    <div class="nav-item">
        <a href="../../src/dashboard/dashboard.php" class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-border-all"></i>
            <span class="label">Dashboard</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="../../src/team_members/team_members.php" class="nav-link <?php echo ($current_page == 'team_members.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-people-group"></i>
            <span class="label">Team</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="../../src/board/kanban_board.php" class="nav-link <?php echo ($current_page == 'kanban_board.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-list-check"></i>
            <span class="label">Kanban</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="../../src/leader_board/leaderboard.php" class="nav-link <?php echo ($current_page == 'leaderboard.php') ? 'active' : ''; ?>">
            <i class="fa-solid fa-award"></i>
            <span class="label">Leaderboard</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="../../src/task_calender/task_calender.php" class="nav-link <?php echo ($current_page == 'task_calender.php') ? 'active' : ''; ?>">
            <i class="fa-regular fa-calendar-check"></i>
            <span class="label">Calendar</span>
        </a>
    </div>
    <div class="nav-item">
        <a href="../../src/profile/profile.php" class="nav-link <?php echo ($current_page == 'profile.php') ? 'active' : ''; ?>">
            <i class="fa-regular fa-user"></i>
            <span class="label">Profile</span>
        </a>
    </div>
</div>
