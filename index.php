<?php
    session_start();
    if(isset($_SESSION['uname'])){
        $uname = $_SESSION['uname'];
        // echo "jidisjd";
    }
    
?>
<?php include('conf/database/db_connect.php'); ?>

<?php include('examples/header.php'); ?>


<h1>Add your contents inside this container</h1>

<?php include('examples/navbar.php'); ?>

<?php include('examples/footer.php'); ?>