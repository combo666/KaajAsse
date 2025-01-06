<?php
include '../../examples/includes/header.php'; 
// include '../../conf/database/db_connect.php';
session_start();

if (!isset($_SESSION['uname'])) {
    die("Access denied. Please log in.");
}
// echo $_SESSION['project_name'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
if (isset($_GET['action']) && $_GET['action'] === 'select_project') {
    // Handle project selection
echo 'req recieved';

    if (isset($_GET['project_name'], $_GET['project_owner'], $_GET['project_id'])) {
        // Save project details to session
        $_SESSION['project_name'] = $_GET['project_name'];
        $_SESSION['project_owner'] = $_GET['project_owner'];
        $_SESSION['project_id'] = $_GET['project_id'];

        // Redirect to the dashboard with a success status
        header("Location: ../dashboard/dashboard.php");
        exit();
    } else {
        // Handle missing parameters
        echo json_encode(["status" => "error", "message" => "Required project details are missing."]);
        exit();
    }
}

}




?>

<?php

if(isset($_POST['logout'])){
session_unset();
session_destroy();

// echo "hey";


if(isset($_SESSION['uname'])){
    echo $_SESSION['uname'];
}else{
    header("Location: ../../index.php");
    exit();

}

}
?>

<form method = "POST" class="butn">

<button type="submit" name="logout">Log out</button>

</form>


<?php include '../../examples/includes/navbar.php'; ?>
<?php include '../../examples/includes/footer.php'; ?>
