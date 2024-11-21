<?php
    session_start();
    if(isset($_SESSION['uname'])){
        $uname = $_SESSION['uname'];
        // echo "jidisjd";
    }
    
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<?php include('conf/database/db_connect.php'); ?>

    <?php
        if(!isset($_SESSION['uname'])){
            echo $_SESSION['uname'];
            ?>
    <?php
        }
    ?>
</body>
</html>