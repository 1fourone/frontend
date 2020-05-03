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
        
        $needsPrint = FALSE;
        if(preg_match("/\s+print\(/", $q->{'studentInput'}) == FALSE)
            $needsPrint = TRUE;
        
        foreach($q->{'testCases'} as $tc) {

            if($needsPrint == TRUE)
                fwrite($file, "print(");
            fwrite($file, $q->{'functionName'} . "(");

            $s = "['1', '2', '3'] 4 (1, 2)";
            $decodedTestInput = rawurldecode($tc[0]);
            preg_match_all('/(\[[-?\S*\.?\S*, "\']+\])|(\{[-?\S\.?, :"\']+\})|(\([-?\S\.?, "\']+\))|(["\'].*["\'])|([-+]?\d+\.?\d*e?[-+]?)/m', $decodedTestInput, $matches);
                            
            //matches[0] holds all "chunks"/arguments to the function
            if(empty($matches[0])) {
                fwrite($file, $decodedTestInput . ")");
                if($needsPrint == TRUE)
                    fwrite($file, ")");
                fwrite($file, "\n");
            }
            else {
                for($i = 0; $i < count($matches[0]); $i++) {
                    if($i != count($matches[0]) -1)
                        fwrite($file, $matches[0][$i] . ", ");
                    else {
                        fwrite($file, $matches[0][$i] . ")");
                        if($needsPrint == TRUE)
                            fwrite($file, ")");
                        fwrite($file, "\n");
                    }
                }
            }

            /*
            /* print arrays/tuples 'as is' 
            if($tc[0][0] == "(" || $tc[0][0] == "[")
                fwrite($file, $tc[0]);
            else
            {
                $args = explode(" ", rawurldecode($tc[0]));
                for($i = 0; $i < count($args); $i++) {
                    fwrite($file, $args[$i]);
                    if($i != (count ($args) - 1))
                        fwrite($file, ",");
                }
            }
            
            fwrite($file, "))\n");
            */
        }
        pclose($file);
    }

    /*  
        evaluateQuestion(q) - evaluates a single question/submission *object*
        returns a JSON string for a graderQuestionOutput object
    */
    function evaluateQuestion($q) {
        
        /* Prepare for non-testcase fields */
        $testResults = array();
        $numOfTests = count($q->{'testCases'});
        $maxPoints = $q->{'maxPoints'};
        $conditions = 3 + $numOfTests;
        $takeOffPoints = floor(($maxPoints/$conditions));
        $result = (object)[];
        
        $result->{'qid'} = $q->{'qid'};
        $result->{'sid'} = $q->{'sid'};
        $result->{'maxPoints'} = $q->{'maxPoints'};

        $inputLines = explode("\n", rawurldecode($q->{'studentInput'}));
        
        // check function name, correct if necessary
        if(strpos($inputLines[0], $q->{'functionName'} . "(") === FALSE) {
            $result->{'name'} = $takeOffPoints;
            $inputLines[0] = preg_replace('(def [a-zA-Z0-9,]+\()', 'def ' . $q->{'functionName'} . "(", $inputLines[0]);
        }
        else 
            $result->{'name'} = 0;

        // check colon name, correct if necessary
        $closeParenIndex = strrpos($inputLines[0], ')');
        if(strlen($inputLines[0]) <= $closeParenIndex+1 || $inputLines[0][$closeParenIndex+1] != ":") {
            $result->{'colon'} = $takeOffPoints;
            $inputLines[0] = substr($inputLines[0], 0, $closeParenIndex+1) . ":";
        }
        else
            $result->{'colon'} = 0;


        // check constraint
        $constraintFind = $q->{'constraintName'};
        if($q->{'constraintName'} === "none") 
            $constraintFind = "return";
        
        if(strpos(rawurldecode($q->{'studentInput'}), $constraintFind) === FALSE)
            $result->{'constraintName'} = $takeOffPoints;
        else
            $result->{'constraintName'} = 0;

        // package q back to itself, with updated lines
        $fixedInput = "";
        foreach($inputLines as $line)
            $fixedInput = $fixedInput . $line . "\n";

        $q->{'studentInput'} = $fixedInput;
        //@TODO: what to do if there's some syntax errors within the code? (ie i++)?
        populateSubmissionFile($q);
        $outputString = shell_exec("python3.8 /tmp/submission.py 2>&1 ");
        $outputLines = explode("\n", $outputString);

        /* Evaluate test cases/output */
        // Handle syntax/etc errors unrelated to colon/name/constraint
        if(preg_match('/.+Error/', $outputString, $matches) == 1)
        {
            //We found an error, and it's $matches[0] - put that as the value for every test and take points off
            $testObject = (object)[];
            
            for($i = 0 ; $i < $numOfTests; $i++) {
                $testObject = (object)['result' => $matches[0], 'lost' => $takeOffPoints];
                array_push($testResults, $testObject);       
            }
        }
        else
        {
            for($i = 0 ; $i < $numOfTests; $i++) {
                //$trueLine = ($q->{'constraintName'} == "print") ? 2 * $i : $i;
                $trueLine = $i;
                $testObject = (object)[];
                $testObject->{'result'} = $outputLines[$trueLine];
                if($outputLines[$trueLine] != rawurldecode($q->{'testCases'}[$i][1]))
                    $testObject->{'lost'} = $takeOffPoints;
                else
                    $testObject->{'lost'} = 0;
                array_push($testResults, $testObject);
            }
        }
        
        $result->{'tests'} = $testResults;
    
        return json_encode($result);
    }
    
    /*  
        If the request is from the tester, just do specific question 
        Otherwise, return the array with every question's graded output 
    */
    $type = $_GET['type'];
    //echo "hi";
    if($type === "test")
        echo evaluateQuestion(json_decode($_GET['question']));
    else {
        $content = file_get_contents("php://input");
        $data = json_decode(strstr($content, "["));
        $output = array();
        foreach ($data as $q) {
            array_push($output, evaluateQuestion($q));
        }
        
        echo json_encode($output);
    }
?>
