<?php
    // $DB['db_host'] = "sql3.freesqldatabase.com";
    // $DB['db_user'] = "sql3746436";
    // $DB['db_pass'] = "nmE9qv5ZjG";
    // $DB['db_name'] = "sql3746436";

    // foreach($DB as $key=>$value)
    // {
    //     define(strtoupper($key), $value);
    // }
    
    $connect = mysqli_connect("sql3.freesqldatabase.com", "sql3746436", "nmE9qv5ZjG", "sql3746436");

    if(!$connect)
    {
        die("db error");
    }
?>