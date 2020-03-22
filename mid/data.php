<?php

    $ch = curl_init();
    $base_url = "http://1fourone.io/webgrader/back/data.php?";
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $data = $_REQUEST['data'];
    if($data == "home")
    {
        if(empty($_GET['instructor']))
        {
            /* student home data is requested */
            curl_setopt($ch, CURLOPT_URL, $base_url . "data=home&student=" . $_GET['student']);
            $output = curl_exec($ch);
            if($output === false)
                echo "Curl error: " . curl_error($ch);
            else
                echo $output;
        }
        else
        {
            /* instructor home data is requested */
            curl_setopt($ch, CURLOPT_URL, $base_url . "data=home&instructor=" . $_GET['instructor']);
            $output = curl_exec($ch);
            if($output === false)
                echo "Curl error: " . curl_error($ch);
            else
                echo $output;
        }
    }

    curl_close($ch);
?>