<?php
  /*
  $arr = array();
  $one = array(
      "qid"=> "26be250f-6af9-11ea-bed6-b827eb031409",
      "sid"=> "a28a71d3-6af8-11ea-bed6-b827eb031409",
      "prompt"=> "front and back ",
      "submissionTest"=> "def front_back(string):\n\tfront = string[0]\n\tback = string[-1]\n\treturn back+string[1:-1]+front",
      "maxPoints"=> 10,
      "firstTestCase"=> "'code'",
      "firstOutput"=> "eodc",
      "secondTestCase"=> "'ab'",
      "secondOutput"=> "ba",  
      "functionSignature"=> "front_back(string)"
  );
  
  $two = array(
      "qid"=> "26be250f-6af9-11ea-bed6-b827eb031409",
      "sid"=> "a28a71d3-6af8-11ea-bed6-b827eb031409",
      "prompt"=> "two parameters makes 10",
      "submissionTest"=> "def makes10(a,b):\n\tsum=a+b\n\tif a==10:\n\t\treturn True\n\tif b==10:\n\t\treturn True\n\tif sum==10:\n\t\treturn True\n\treturn False",
      "maxPoints"=> 10,
      "firstTestCase"=> "5,5",
      "firstOutput"=> "True",
      "secondTestCase"=> "5,10",
      "secondOutput"=> "True",  
      "functionSignature"=> "makes10(a,b)"
  );
  
  $three = array(
      "qid"=> "26be250f-6af9-11ea-bed6-b827eb031409",
      "sid"=> "a28a71d3-6af8-11ea-bed6-b827eb031409",
      "prompt"=> "add 2 numbers",
      "submissionTest"=> "def add(a,b):\n\t return a+b",
      "maxPoints"=> 10,
      "firstTestCase"=> "-2,-5",
      "firstOutput"=> "-7",
      "secondTestCase"=> "5,10",
      "secondOutput"=> "15",  
      "functionSignature"=> "add(a,b)"
  );
  
  $four = array(
      "qid"=> "26be250f-6af9-11ea-bed6-b827eb031409",
      "sid"=> "a28a71d3-6af8-11ea-bed6-b827eb031409",
      "prompt"=> "Given an array of ints length 3, return the sum of all the elements.",
      "submissionTest"=> "def sum3(arr):\n\treturn arr[0]+arr[1]+arr[2]",
      "maxPoints"=> 10,
      "firstTestCase"=> "[1,2,3]",
      "firstOutput"=> "6",
      "secondTestCase"=> "[5,11,2]",
      "secondOutput"=> "18",  
      "functionSignature"=> "sum3(arr)"
  );
  
  $five = array(
      "qid"=> "26be250f-6af9-11ea-bed6-b827eb031409",
      "sid"=> "a28a71d3-6af8-11ea-bed6-b827eb031409",
      "prompt"=> "Given an int n, return the absolute difference between n and 21, except return double the absolute difference if n is over 21.",
      "submissionTest"=> "def ndiff21(num):\n\tif num<=21:\n\t\treturn 21-num\n\tif num>21:\n\t\tnum = num-21\n\t\treturn num*2",
      "maxPoints"=> 10,
      "firstTestCase"=> "19",
      "firstOutput"=> "2",
      "secondTestCase"=> "21",
      "secondOutput"=> "0",  
      "functionSignature"=> "ndiff21(num)"
  );
  
  $six = array(
      "qid"=> "26be250f-6af9-11ea-bed6-b827eb031409",
      "sid"=> "a28a71d3-6af8-11ea-bed6-b827eb031409",
      "prompt"=> "subtract 2 numbers",
      "submissionTest"=> "defTree sub(a,b):\n\treturn a-b",
      "maxPoints"=> 10,
      "firstTestCase"=> "10,30",
      "firstOutput"=> "-20",
      "secondTestCase"=> "5,5",
      "secondOutput"=> "0",  
      "functionSignature"=> "sub(a,b)"
  );
  
  $seven = array(
      "qid"=> "26be250f-6af9-11ea-bed6-b827eb031409",
      "sid"=> "a28a71d3-6af8-11ea-bed6-b827eb031409",
      "prompt"=> "Write a function twice(a,b) that returns the twice the result of adding a and b",
      "submissionTest"=> "def twice(a,b):\n\tsum = a+b\n\tsum = 2*sum\n\treturn sum",
      "maxPoints"=> 10,
      "firstTestCase"=> "5,10",
      "firstOutput"=> "30",
      "secondTestCase"=> "2,2",
      "secondOutput"=> "8",  
      "functionSignature"=> "twice(a,b)"
  );
  
  $eight = array(
      "qid"=> "26be250f-6af9-11ea-bed6-b827eb031409",
      "sid"=> "a28a71d3-6af8-11ea-bed6-b827eb031409",
      "prompt"=> "Write a function, reverse(string) that returns a string reversed.",
      "submissionTest"=> "def reverse(string):\n\treturn string[::-1]",
      "maxPoints"=> 10,
      "firstTestCase"=> "'cow'",
      "firstOutput"=> "woc",
      "secondTestCase"=> "'alpha'",
      "secondOutput"=> "ahpla",  
      "functionSignature"=> "reverse(string)"
  );
  
  array_push($arr, $one);
  array_push($arr, $two);
  array_push($arr, $three);
  array_push($arr, $four);
  array_push($arr, $five);
  array_push($arr, $six);
  array_push($arr, $seven);
  array_push($arr, $eight);
  
  $json = json_encode($arr);
  */
  //var_dump($_POST['data']);
  $array = json_decode($_POST['data']);
  //var_dump($array);
  //var_dump("---", count($array));
  $sizeOfArray = count($array);
  $arrToSend = array();

  for($i=0; $i<$sizeOfArray; $i++){ 
  
    $specificSubmissionArr = array(
      "firstPassed" => "",
      "secondPassed" => "",
      "ranSuccessfully" => "",
      "correctSignature" => "",
      "hasReturn" => "",
      "pointsLost" => [ ]
    );
    
    
    $prompt = $array[$i]->{"prompt"};
    $answer = $array[$i]->{"submissionText"};
    $points = $array[$i]->{"maxPoints"};
    $TS1 = $array[$i]->{"firstTestCase"};
    $O1 = $array[$i]->{"firstOutput"};
    $TS2 = $array[$i]->{"secondTestCase"};
    $O2 = $array[$i]->{"secondOutput"};
    $functionSignatureBefore = $array[$i]->{"functionSignature"};
    $pieces = explode("(",$functionSignatureBefore);
    $functionSignature = $pieces[0];
  
    //var_dump("ANSWER " . $answer);
  //save the ORIGINAL student answer for testcase2 
    $saveThisForTestCase2 = $answer;
    
    //add testcase 1 to submission
    $answer = $answer."\n";
    $answer = $answer."answer = ";
    $answer = $answer.$functionSignature;
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
    $answer = $answer.$functionSignature;
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
    
    if(strcmp($string1, $O1) == 0){
      $specificSubmissionArr["firstPassed"] = "true";
      $specificSubmissionArr["pointsLost"][0] = 0;
    }else{
      $specificSubmissionArr["firstPassed"] = "false";
      $specificSubmissionArr["pointsLost"][0] += .2 * $points;
    }
    
    if(strcmp($string2, $O2) == 0){
      $specificSubmissionArr["secondPassed"] = "true";
      $specificSubmissionArr["pointsLost"][1] = 0;
    }else{
      $specificSubmissionArr["secondPassed"] = "false";
      $specificSubmissionArr["pointsLost"][1] = .2 * $points;
    }
    
    if(preg_match("/\bTraceback\b/i", $output1) or preg_match("/\bTraceback\b/i", $output2) or preg_match("/\bSyntaxError\b/i", $output1) or preg_match("/\bSyntaxError\b/i", $output2)){
      $specificSubmissionArr["ranSuccessfully"] = "false";
      $specificSubmissionArr["pointsLost"][2] = .2 * $points;
    }else{
      $specificSubmissionArr["ranSuccessfully"] = "true";
      $specificSubmissionArr["pointsLost"][2] = 0;
    }
    
    if(!preg_match("/\b.$functionSignature.\b/i", $saveThisForTestCase2)){
      $specificSubmissionArr["correctSignature"] = "false";
      $specificSubmissionArr["pointsLost"][3] = .2 * $points;
    }else{
      $specificSubmissionArr["correctSignature"] = "true";
      $specificSubmissionArr["pointsLost"][3] = 0;
    }
  
    if(!preg_match("/\breturn\b/i", $saveThisForTestCase2)){
      $specificSubmissionArr["hasReturn"] = "false";
      $specificSubmissionArr["pointsLost"][4] = .2 * $points;
    }else{
      $specificSubmissionArr["hasReturn"] = "true";
      $specificSubmissionArr["pointsLost"][4] = 0;
    }
    
    $array[$i]->{'autoFeedback'} = (object)$specificSubmissionArr;
    //var_dump($specificSubmissionArr);
    //array_push($arrToSend, (object)$specificSubmissionArr);
  }

  $j = json_encode($arrToSend);
  echo json_encode($array);
  
?>