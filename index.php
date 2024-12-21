<?php
    session_start();
    // $_SESSION['uname'] = "evan@gmail.com";
    // $uname = $_SESSION['uname'];
    if(isset($_SESSION['uname'])){
        $uname = $_SESSION['uname'];
        // echo $uname;
    }
?>
<?php include('conf/database/db_connect.php'); ?>


<?php
    if(isset($_SESSION['uname']))
    {
        // include './examples/includes/header.php';
     
        // include './examples/includes/navbar.php';
        session_unset();
        session_destroy();
        header("Location: src/projects/index.php");
        exit();
    }
    else
    {
        header('location:src/login/login_reg.php');
    }
?>


<!-- <?php

    $query = "SELECT * FROM KaajAsse.user;";

    $stmt = mysqli_query($connect, $query);
    while($row = mysqli_fetch_array($stmt, MYSQLI_ASSOC)){
        echo $row['user_id']."<br>";
    }

?> -->


<?php include('./examples/includes/footer.php'); ?>