<?php

session_start();
session_unset();
session_destroy();
if(isset($_SESSION['uname'])){
    echo $_SESSION['uname'];
}else{
    header("Location: index.php");
    exit();

}

?>