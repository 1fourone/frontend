<?php

    /*  
        populateSubmissionFile($submission) - will prepare the contents of `submission.py`
        the grader will then take advantage of the file to run checks against it
        populating this file should NEVER fail as grading depends on it
        thus, if this fails, throw error and die.
    */
    function populateSubmissionFile($q) {
        $file = fopen("/tmp/submission.py", "w") or die ("unable to open submissions file");
        fwrite($file, $q->{'studentInput'} . "\n");
        foreach($q->{'testCases'} as $tc) {
            fwrite($file, "print(" . $q->{'functionName'} . "(");
            
            /* print arrays/tuples 'as is' */
            if($tc[0][0] == "(" || $tc[0][0] == "[")
                fwrite($file, $tc[0]);
            else
            {
                $args = explode(" ", $tc[0]);
                for($i = 0; $i < count($args); $i++) {
                    fwrite($file, $args[$i]);
                    if($i != (count ($args) - 1))
                        fwrite($file, ",");
                }
            }
            fwrite($file, "))\n");
        }
        pclose($file);
    }

    /*  
        evaluateQuestion(q) - evaluates a single question/submission *object*
        returns a JSON string for a graderQuestionOutput object
    */
    function evaluateQuestion($q) {
        populateSubmissionFile($q);
        $outputString = shell_exec("python3.8 /tmp/submission.py 2>&1 ");
        $outputLines = explode("\n", $outputString);
        $result = (object)[];

        /* Error handling */
        $errors = array("Error", "Interrupt", "Traceback");
        $errorFound = false;
        foreach($errors as $err) {
            if(strstr($outputString, $err))
                $errorFound = true;
        }

        /* Evaluate test cases/output */
        $testResults = array();
        $numOfTests = count($q->{'expectedOutput'}->{'tests'});

        if($errorFound == true) {
            for($i = 0; $i < $numOfTests; $i++)
                array_push($testResults, 1);
        }
        else {
            //check tests
            for($i = 0 ; $i < $numOfTests; $i++) {
                $trueLine = ($q->{'constraint'} == "print") ? 2 * $i : $i;
                if($outputLines[$trueLine] != $q->{'testCases'}[$i][1])
                    array_push($testResults, 1);
                else
                    array_push($testResults, 0);
            }
        }
        $result->{'tests'} = $testResults;

        /* Evaluate remaining criteria */
        if(!strstr($q->{'studentInput'}, $q->{'functionName'}))
            $result->{'name'} = 1;
        else 
            $result->{'name'} = 0;

        $studentLines = explode("\n", $q->{'studentInput'});
        if($studentLines[0][strlen($studentLines[0])-1] != ":") 
            $result->{'colon'} = 1;
        else
            $result->{'colon'} = 0;

        if(!strstr($q->{'studentInput'}, $q->{'constraint'}))
            $result->{'constraint'} = 1;
        else
            $result->{'constraint'} = 0;

        return json_encode($result);
    }
    
    /*  
        If the request is from the tester, just do specific question 
        Otherwise, return the array with every question's graded output 
    */
    $type = $_GET['type'];
    if($type === "test")
        echo evaluateQuestion(json_decode($_GET['question']));
    else {
        $data = json_decode($_GET['data']);
        $output = array();

        foreach ($data as $q) {
            array_push($output, evaluateQuestion($q));
        }
        
        echo json_encode($output);
    }
?>