<?php
    $DB['db_host'] = "sql3.freesqldatabase.com";
    $DB['db_user'] = "sql3746436";
    $DB['db_pass'] = "nmE9qv5ZjG";
    $DB['db_name'] = "sql3746436";

    foreach($DB as $key=>$value)
    {
        define(strtoupper($key), $value);
    }
    
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if(!$connect)
    {
        die("db error");
    }
?>