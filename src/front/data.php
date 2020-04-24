<?php

    $ch = curl_init();
    //$base_url = "https://web.njit.edu/~as2863/RC/data.php?";
    $base_url = "http://localhost/mid/data.php?";
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
    else if($data == "question")
    {
        if(!empty($_POST['question']))
        {
            /* inserting a question to bank */
            curl_setopt($ch, CURLOPT_URL, $base_url);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            $content = file_get_contents("php://input");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
            $output = curl_exec($ch);
            if($output === false)
                echo "Curl error: " . curl_error($ch);
            else
                echo $output;
        }
    }
    else if($data == "bank")
    {
        /* getting all questions from the bank */
        curl_setopt($ch, CURLOPT_URL, $base_url . "data=bank");
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
            echo $output;
    }
    else if($data == "exam")
    {
        if(!empty($_POST['exam']))
        {
            /* inserting an exam */
            curl_setopt($ch, CURLOPT_URL, $base_url);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "data=exam&exam=" . $_POST['exam']);
            $output = curl_exec($ch);
            if($output === false)
                echo "Curl error: " . curl_error($ch);
            else
                echo $output;
        }
        else if(!empty($_GET['id']))
        {
            /* getting an exam's info */
            curl_setopt($ch, CURLOPT_URL, $base_url . "data=exam&id=" . $_GET['id']);
            $output = curl_exec($ch);
            if($output === false)
                echo "Curl error: " . curl_error($ch);
            else
                echo $output;
        }
    }
    else if($data == "autograde")
    {
        /* autograding the exam */
        curl_setopt($ch, CURLOPT_URL, $base_url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "data=autograde&id=" . $_POST['id']);
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
            echo $output;
    }
    else if($data == "exams")
    {
        if(!empty($_GET['id']))
        {
            /* requesting all the exam info for a particular exam for review */
            if($_GET['type'] == "instructor")
                curl_setopt($ch, CURLOPT_URL, $base_url . "data=exams&type=instructor&id=" . $_GET['id']);
            else
                curl_setopt($ch, CURLOPT_URL, $base_url . "data=exams&type=student&id=" . $_GET['id']);
            $output = curl_exec($ch);
            if($output === false)
                echo "Curl error: " . curl_error($ch);
            else
                echo $output;
        }
        else if(!empty($_POST['id']))
        {
            /* updating all exams based on professor feedback */
            curl_setopt($ch, CURLOPT_URL, $base_url);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "data=exams&id=" . $_POST['id'] . "&content=" . $_POST['content']);
            $output = curl_exec($ch);
            if($output === false)
                echo "Curl error: " . curl_error($ch);
            else
                echo $output;
        }
    }
    else if($data == "release")
    {
        /* updating all exams to be released */
        curl_setopt($ch, CURLOPT_URL, $base_url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "data=release&id=" . $_POST['id']);
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
            echo $output;
    }
    else if($data == "review")
    {
        /* requesting specific exam info for a particular exam for review */
        curl_setopt($ch, CURLOPT_URL, $base_url . "data=review&id=" . $_GET['id'] . "&content=" . $_GET['content']);
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
            echo $output;
    }
    else if($data == "take")
    {
        /* requesting specific exam info for a particular exam for review */
        curl_setopt($ch, CURLOPT_URL, $base_url . "data=take&id=" . $_GET['id'] . "&content=" . $_GET['content']);
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
            echo $output;
    }
    else if($data == "submit")
    {
        /* student submits exam */
        curl_setopt($ch, CURLOPT_URL, $base_url);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        $content = file_get_contents("php://input");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        $obj = json_decode($_POST['content']);

        //echo urldecode($_POST['content']);
        
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
            echo $output;
    }

    curl_close($ch);
?>