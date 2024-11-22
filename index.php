<?php
    session_start();
    if(isset($_SESSION['uname'])){
        $uname = $_SESSION['uname'];
        // echo "jidisjd";
    }
    
?>
<?php include('conf/database/db_connect.php'); ?>

<?php include('./examples/includes/header.php'); ?>


<h1>Add your contents inside this container</h1>

<?php include('./examples/includes/navbar.php'); ?>

<?php include('./examples/includes/footer.php'); ?>