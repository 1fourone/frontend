<?php

  
  //hardcoded JSON array
  $json = '{
                "1" : [{
                  "prompt":"front and back ",
                  "studentSubmission":"def front_back(string):\n\tfront = string[0]\n\tback = string[-1]\n\treturn back + string[1:-1] + front",
                  "maxPoints":"10",
                  "testCaseOne":"\'code\'",
                  "outputOne":"eodc",
                  "testCaseTwo":"\'ab\'",
                  "outputTwo":"ba",
                  "autoFeedback":"",
                  "functionName":"front_back"
                }], 
                "2":[{
                  "prompt":"two parameters makes 10",
                  "studentSubmission":"def makes10(a,b):\n\tsum=a+b\n\tif a==10:\n\t\treturn True\n\tif b==10:\n\t\treturn True\n\tif sum==10:\n\t\treturn True\n\treturn False",
                  "maxPoints":"10",
                  "testCaseOne":"5,5",
                  "outputOne":"True",
                  "testCaseTwo":"5,10",
                  "outputTwo":"True",
                  "autoFeedback":"",
                  "functionName":"makes10"
                }],
                "3":[{
                  "prompt":"add 2 numbers",
                  "studentSubmission":"def add(a,b):\n\t return a+b",
                  "maxPoints":"10",
                  "testCaseOne":"-2,-5",
                  "outputOne":"-7",
                  "testCaseTwo":"5,10",
                  "outputTwo":"15",
                  "autoFeedback":"",
                  "functionName":"add"
                }],
                "4":[{
                  "prompt":"Given an array of ints length 3, return the sum of all the elements.",
                  "studentSubmission":"def sum3(arr):\n\treturn arr[0]+arr[1]+arr[2]",
                  "maxPoints":"10",
                  "testCaseOne":"[1,2,3]",
                  "outputOne":"6",
                  "testCaseTwo":"[5,11,2]",
                  "outputTwo":"18",
                  "autoFeedback":"",
                  "functionName":"sum3"
                }]
  }'; 
  
  //convert json into array
  $array = json_decode($json, true);
  
  //size of questions
  $sizeOfArray = count($array) + 1;
  
  
  //go through all submissions
  for($i = 4; $i<5; $i++){
  
    //boolean that checks and sees if the student answer function works
    $totalPoints = 10;
    
    //assign values from the array to variables
    $prompt = $array[$i][0]["prompt"];
    $answer = $array[$i][0]["studentSubmission"];
    $points = $array[$i][0]["maxPoints"];
    $TS1 = $array[$i][0]["testCaseOne"];
    $O1 = $array[$i][0]["outputOne"];
    $TS2 = $array[$i][0]["testCaseTwo"];
    $O2 = $array[$i][0]["outputTwo"];
    $functionName = $array[$i][0]["functionName"];
    
    
    //save the ORIGINAL student answer for testcase2 
    $saveThisForTestCase2 = $answer;
    
    //add testcase 1 to submission
    $answer = $answer."\n";
    $answer = $answer."answer = ";
    $answer = $answer.$functionName;
    $answer = $answer."(";
    $answer = $answer.$TS1;
    $answer = $answer.")";
    $answer = $answer."\n";
    $answer = $answer."print answer";
    
    //see if testcase1 runs
    $filename = "main.py";
    $handle = fopen($filename, "w") or die ("unable to read");
    fwrite($handle, $answer);
    pclose($handle);
    $output1 = shell_exec('python main.py 2>&1 ');
    unlink($filename);
    
    //add testcase 2 to submission
    $answer = $saveThisForTestCase2;
    $answer = $answer."\n";
    $answer = $answer."answer = ";
    $answer = $answer.$functionName;
    $answer = $answer."(";
    $answer = $answer.$TS2;
    $answer = $answer.")";
    $answer = $answer."\n";
    $answer = $answer."print answer";
    
    //see if testcase2 runs
    $ifRuns = True;
    $filename = "main.py";
    $handle = fopen($filename, "w") or die ("unable to read");
    fwrite($handle, $answer);
    pclose($handle);
    $output2 = shell_exec('python main.py 2>&1 ');
    unlink($filename);
    
    //check if output matches
    $test1 = $output1;
    $test2 = $output2;
    $length1 = strlen($output1);
    $length2 = strlen($output2);
    $string1 = substr($test1, 0, $length1-1);
    $string2 = substr($test2, 0, $length2-1);
    if(strcmp($string1,$O1) == 0 and strcmp($string2,$O2) == 0){
      $array[$i][0]["autoFeedback"] = $array[$i][0]["autoFeedback"]." Great job! Your function is correct! ";
    }else{
      $array[$i][0]["autoFeedback"] = $array[$i][0]["autoFeedback"]." Your answer is incorrect! ";
    }
    
    //see if there is syntax errors
    if(preg_match("/\bTraceback\b/i", $output1) and preg_match("/\bTraceback\b/i", $output2)){
      $array[$i][0]["autoFeedback"] = $array[$i][0]["autoFeedback"]." -5 Incorrect Syntax ";
      $totalPoints = $totalPoints - 5;
    } 
    
    //see if function name is correct
    if(!preg_match("/\b.$functionName.\b/i", $saveThisForTestCase2)){
      $array[$i][0]["autoFeedback"] = $array[$i][0]["autoFeedback"]." -2 Wrong Function Name ";
      $totalPoints = $totalPoints - 2;
    }
    
    //see if there is a return function 
    if(!preg_match("/\breturn\b/i", $saveThisForTestCase2)){
      $array[$i][0]["autoFeedback"] = $array[$i][0]["autoFeedback"]." -2 No Return Statement ";
      $totalPoints = $totalPoints - 2;
    }
    
    //Total Points add up!
    $array[$i][0]["autoFeedback"] = $array[$i][0]["autoFeedback"]." Total Points Earned:".$totalPoints;
    
    
    
  }
  
  
  //echo $array[1][0]["autoFeedback"];
  //echo $array[2][0]["autoFeedback"];
  //echo $array[3][0]["autoFeedback"];
  echo $array[4][0]["autoFeedback"];






?>
