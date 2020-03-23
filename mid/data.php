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
    else if($data == "question")
    {
        if(!empty($_POST['question']))
        {
            /* inserting a question to bank */
            curl_setopt($ch, CURLOPT_URL, $base_url);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "data=question&question=" . $_POST['question']);
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
        /* Here mid will send a get request to back first to get the exam info
            for this exam id it needs */
        $examID = $_POST['id'];
        curl_setopt($ch, CURLOPT_URL, $base_url . "data=autograde&id=" . $examID);
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
        {
            //echo $output; //JSON string with exam info necessary for grader
            /* got the data successfully, curl to grader here for results */
            /* send a POST request to grader to retrieve "updated" examInfo */
            curl_setopt($ch, CURLOPT_URL, "http://1fourone.io/webgrader/mid/grader.php?");
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "data=" . $output);
            //curl_setopt($ch, CURLOPT_URL, "data=" . json_encode($output));
            $output = curl_exec($ch);
            if($output === false)
                echo "Curl error: " . curl_error($ch);
            else
            {
                /* TODO: got the updated data back successfully, send a POST to back to update */
                curl_setopt($ch, CURLOPT_URL, $base_url);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                //var_dump("data=autograde&id=" . $examID . "&data=" . $output);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "data=autograde&id=" . $examID . "&content=" . $output);
                $output = curl_exec($ch);
                if($output === false)
                    echo "Curl error: " . curl_error($ch);
                else
                {
                    /* got the back's result here, send it to front */
                    echo $output;
                }
            }
        }
    }
    else if($data == "exams")
    {
        if(!empty($_GET['id']))
        {
            /* requesting all the exam info for a particular exam for review */
            curl_setopt($ch, CURLOPT_URL, $base_url . "data=exams&id=" . $_GET['id']);
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
        curl_setopt($ch, CURLOPT_POSTFIELDS, "data=submit&id=" . $_POST['id'] . "&content=" . $_POST['content']);
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
            echo $output;
    }

    curl_close($ch);
?>