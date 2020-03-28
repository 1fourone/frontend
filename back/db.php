<?php

    $dbServer = "1fourone.io";
    $dbUser = "webster";
    $dbPass = "490project";
    $dbName = "webgrader";

    $handle = mysqli_connect($dbServer, $dbUser, $dbPass, $dbName);
    if(!$handle)
        die("Connection failed: " . mysqli_connect_error());

    /* Query stuff here */

    mysqli_close($handle);
?>