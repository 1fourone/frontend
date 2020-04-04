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
            $args = explode(" ", $tc[0]);
            for($i = 0; $i < count($args); $i++) {
                fwrite($file, $args[$i]);
                if($i != (count ($args) - 1))
                    fwrite($file, ",");
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
        $output = explode("\n", shell_exec("/usr/bin/python /tmp/submission.py 2>&1 "));
        $result = (object)[];

        /* Populate the graderQuestionOutput here */
        $conditions = 3 + count($q->{'expectedOutput'}->{'tests'});
    
        /* placeholder here, should loop through conditions and set fields accordingly */ 
        $result->{'name'} = 0;
        $result->{'constraint'} = 0;
        $result->{'colon'} = 0;
        $result->{'tests'} = [1, 1, 1];

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