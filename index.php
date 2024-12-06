<?php
    session_start();
    $_SESSION['uname'] = "evan@gmail.com";
    $uname = $_SESSION['uname'];
    // if(isset($_SESSION['uname'])){
    //     $uname = $_SESSION['uname'];
    //     // echo "jidisjd";
    // }
    
?>
<?php include('conf/database/db_connect.php'); ?>

<?php include('./examples/includes/header.php'); ?>


<h1>Add your contents inside this container</h1>

<?php

    $query = "SELECT * FROM KaajAsse.user;";

    $stmt = mysqli_query($connect, $query);
    while($row = mysqli_fetch_array($stmt, MYSQLI_ASSOC)){
        echo $row['user_id'];
    }

?>

<?php include('./examples/includes/navbar.php'); ?>

<?php include('./examples/includes/footer.php'); ?>