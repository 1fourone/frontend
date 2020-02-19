<?php
    //should be able to access _POST['credentials'] to get credentials sent by front
    //password comes hashed thru sha256 already!
    //example file for mid/back to see flow
    $v = $_POST['credentials'];
    //echo $v;

    // ... some processing


    // return result object
    $result = '{"njit":"failure", "local":"failure"}';
    echo $result;
?>