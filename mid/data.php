<?php

    $req = $_REQUEST["value"];

    //prepare a cURL object to communicate with middle
    $ch = curl_init();

    //Caller wants the question bank
    if($req == "qbank")
    {
        curl_setopt($ch, CURLOPT_URL, "http://1fourone.io/webgrader/back/data.php?value=" . $req);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $output = curl_exec($ch);
        curl_close($ch);

        if($output === false)
            echo 'Curl error: ' . curl_error($ch);
        else
            echo $output;
    }
    //Caller wants the exams data
    else if($req == "exams")
    {
        if(isset($_REQUEST['student'])) //Student requested this
            curl_setopt($ch, CURLOPT_URL, "http://1fourone.io/webgrader/back/data.php?value=" . $req . "&student=" . $_REQUEST['student']);
        else //Instructor requested this
            curl_setopt($ch, CURLOPT_URL, "http://1fourone.io/webgrader/back/data.php?value=" . $req . "&instructor=" . $_REQUEST['instructor']);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $output = curl_exec($ch);
        curl_close($ch);

        if($output === false)
            echo 'Curl error: ' . curl_error($ch);
        else
            echo $output;
    }

?>