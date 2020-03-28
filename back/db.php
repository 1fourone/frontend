<?php

    $dbServer = "sql1.njit.edu";
    $dbUser = "spp34";
    $dbPass = "Discovery05!";
    $dbName = "spp34";

    $handle = mysqli_connect($dbServer, $dbUser, $dbPass, $dbName);
    if(!$handle)
        die("Connection failed: " . mysqli_connect_error());

    /* Query stuff here */

    mysqli_close($handle);
?>