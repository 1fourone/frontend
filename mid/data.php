<?php

    $req = $_REQUEST["value"];

    //Caller wants the question bank
    if($req == "qbank")
    {
        //pass the request along to back
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, "http://1fourone.io/webgrader/back/data.php?value=" . $req);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $output = curl_exec($ch);
        curl_close($ch);

        if($output === false)
            echo 'Curl error: ' . curl_error($ch);
        else
            echo $output;
    }

?>