<?php

    $ch = curl_init();
    $base_url = "http://localhost/back/data.php?"; 
    //$base_url = "https://web.njit.edu/~spp34/RC/data.php?";
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
        /* Here mid will send a get request to back first to get the exam info
            for this exam id it needs */
        $examID = $_POST['id'];
        //var_dump($examID);
        curl_setopt($ch, CURLOPT_URL, $base_url . "data=autograde&id=" . $examID);
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
        {
            //echo $output; //JSON string with exam info necessary for grader
            //var_dump($output);
            /* got the data successfully, curl to grader here for results */
            /* send a POST request to grader to retrieve "updated" examInfo */
            //curl_setopt($ch, CURLOPT_URL, "grader.php?");
            //var_dump($output);
            
            $A1 = json_decode($output);
            foreach ($A1 as $qdata){
              $qdata->{'studentInput'} = $qdata->{'submissionText'};
              $testarray = array();
              $a= array($qdata->{'firstTestCase'}, $qdata->{'firstOutput'});
              array_push($testarray, $a);
              $a= array($qdata->{'secondTestCase'}, $qdata->{'secondOutput'});
              array_push($testarray, $a);
              
              if(!empty($qdata->{'thirdTestCase'})){
                $a= array($qdata->{'thirdTestCase'}, $qdata->{'thirdOutput'});
                array_push($testarray, $a);
              }
              
              if(!empty($qdata->{'fourthTestCase'})){
                $a= array($qdata->{'fourthTestCase'}, $qdata->{'fourthOutput'});
                array_push($testarray, $a);
              }
              
              if(!empty($qdata->{'fifthTestCase'})){
                $a= array($qdata->{'fifthTestCase'}, $qdata->{'fifthOutput'});
                array_push($testarray, $a);
              }
              
              if(!empty($qdata->{'sixthTestCase'})){
                $a= array($qdata->{'sixthTestCase'}, $qdata->{'sixthOutput'});
                array_push($testarray, $a);
              }
              
              
              $qdata->{"testCases"} = $testarray;
            }
            //var_dump(json_encode($A1));
                   
            
            //curl_setopt($ch, CURLOPT_URL, "https://web.njit.edu/~as2863/RC/grader2.php?");
            $grader_url = "http://localhost/mid/grader.php?";
            curl_setopt($ch, CURLOPT_URL, $grader_url);

            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "data=" . json_encode($A1) );
            //var_dump($output);
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
                //var_dump("RETURN OF GRADER: " . $output);
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
            //echo $_GET['type'];
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
        
        $output = curl_exec($ch);
        if($output === false)
            echo "Curl error: " . curl_error($ch);
        else
            echo $output;
        
    }

    curl_close($ch);
?>