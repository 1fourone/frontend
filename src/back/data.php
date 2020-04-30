<?php

    /* Prepare SQL connection */
    $dbServerName = "1fourone.io";
    $dbUser = "webster";
    $dbPassword = "490project";
    $dbName = "webgrader";
    $conn = mysqli_connect($dbServerName, $dbUser, $dbPassword, $dbName);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    /* Data related logic from request here */
    $data = $_REQUEST["data"];
    
    if($data == "home")
    {
        if(empty($_GET['instructor']))
        {
            /* student home data is requested */
            $classList = array();
            $sql = sprintf("SELECT c.id, c.course, c.section FROM CLASS c, STUDENT s WHERE s.cid=c.id AND s.id='%s';", $_GET['student']);
            $result = $conn->query($sql);
            while(($c = $result->fetch_assoc()) != NULL)
                array_push($classList, (object)$c);

            $sql = sprintf("SELECT DISTINCT e.id, e.name, e.date, e.status, c.course, c.section FROM EXAM e, CLASS c WHERE e.sid='%s' AND c.id=e.cid GROUP BY e.id ORDER BY e.status DESC;", $_GET['student']);
            $result = $conn->query($sql);
            $examList = array();
            while(($e = $result->fetch_assoc()) != NULL)
                array_push($examList, (object)$e);

            echo "[" . json_encode($classList) . "," . json_encode($examList) . "]";
        }
        else
        {
            /* instructor home data is requested */
            $classList = array();
            
            $sql = sprintf("SELECT c.id, c.course, c.section FROM CLASS c, INSTRUCTOR i WHERE i.cid=c.id AND i.id='%s';", $_GET['instructor']);
            $result = $conn->query($sql);
            while(($c = $result->fetch_assoc()) != NULL)
                array_push($classList, (object)$c);

            $examList = array();
            
            $sql = sprintf("SELECT DISTINCT e.id, e.name, e.date, c.course, c.section FROM EXAM e, CLASS c, INSTRUCTOR i WHERE i.id='%s' AND i.cid=c.id AND e.cid=c.id", $_GET['instructor']);
            $result = $conn->query($sql);
            while(($e = $result->fetch_assoc()) != NULL)
                array_push($examList, (object)$e);

            echo "[" . json_encode($classList) . "," . json_encode($examList) . "]";
        }
    }
    else if($data == "question")
    {
        if(!empty($_POST['question']))
        {
            /* inserting a question to bank */
	   // var_dump($_POST['question']);
	    
	    $content = file_get_contents("php://input");
            //var_dump($content);
	    
	    $question = json_decode(strstr($content, "{"));

            $timestamp = date("Y-m-d H:i:s");
            $sql = sprintf("INSERT INTO QUESTION(id, prompt, functionName, difficulty, topic, constraintName, creatorID, firstTestCase, firstOutput, secondTestCase, secondOutput,
	    thirdTestCase, thirdOutput, fourthTestCase, fourthOutput, fifthTestCase, fifthOutput, sixthTestCase, sixthOutput, creationDate) VALUES(UUID(), '%s',
	    '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
                $question->{'prompt'}, $question->{'functionName'}, $question->{'difficulty'}, $question->{'topic'}, $question->{'constraintName'}, $question->{'creatorID'}, $question->{'firstTestCase'}, 
                $question->{'firstOutput'}, $question->{'secondTestCase'}, $question->{'secondOutput'}, $question->{'thirdTestCase'}, $question->{'thirdOutput'},
		$question->{'fourthTestCase'}, $question->{'fourthOutput'}, $question->{'fifthTestCase'}, $question->{'fifthOutput'}, $question->{'sixthTestCase'},
		$question->{'sixthOutput'}, $timestamp);
            //var_dump($sql);
            $result = $conn->query($sql);
            echo ($result === false) ? "failure" : "success";
        }
    }
    else if($data == "bank")
    {
        /* getting all questions from the bank */
        $questions = array();
        $sql = "SELECT q.id, q.prompt, q.difficulty, q.topic, i.uname AS creatorName, q.creationDate, q.constraintName FROM QUESTION q, INSTRUCTOR i WHERE q.creatorID=i.id;";
        $result = $conn->query($sql);
        while(($q = $result->fetch_assoc()) != NULL)
            array_push($questions, (object)$q);
        echo json_encode($questions);
    }
    else if($data == "exam")
    {
        if(!empty($_POST['exam']))
        {
            /* inserting an exam */
            $examList = json_decode($_POST['exam']);
            //var_dump($examList);

            /* get the student ids in the class */
            $sql = sprintf("SELECT s.id FROM STUDENT s, CLASS c WHERE s.cid=c.id AND c.id='%s';", $examList[0]->{'cid'});
            //echo $sql;
            $result = $conn->query($sql);

            $students = array();
            /* For every student in the class */
            while(($s = $result->fetch_assoc()) != NULL)
                array_push($students, $s);

                
            $result = $conn->query("SELECT UUID();");
            $examID = $result->fetch_row()[0];
            $timestamp = date("Y-m-d H:i:s");

            /* TRANSACTION - insert each question to all members in class */
            $conn->autocommit(FALSE);
            $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
            
            /* INSERT INTO EXAM(id, name, qid, sid, cid, status, maxPoints) */
            for($i = 0; $i < count($examList); $i++)
            {
                for($j = 0; $j < count($students); $j++)
                {
                    $sql = sprintf("INSERT INTO EXAM(id, name, qid, sid, cid, status, maxPoints, date) VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
                    $examID, $examList[$i]->{'name'}, $examList[$i]->{'id'}, $students[$j]['id'], $examList[$i]->{'cid'}, 4, $examList[$i]->{'maxPoints'}, $timestamp);
                    //var_dump($sql);
                    $conn->query($sql);
                }
            }
            $result = $conn->commit();

            echo ($result === true) ? "success" : "failure";
        }
        else if(!empty($_GET['id']))
        {
            /* getting an exam's info */
            $sql = sprintf("SELECT DISTINCT e.name, c.course, c.section, e.status FROM EXAM e, CLASS c WHERE e.cid =c.id AND e.id='%s';", $_GET['id']);
            $result = $conn->query($sql); 
            $examInfo = $result->fetch_assoc();
            echo json_encode((object)$examInfo);
        }
    }
    else if($data == "autograde")
    {
        if(!empty($_GET['id']))
        {
            /* getting the exam needed info for autograding */
            $sql = sprintf("SELECT e.qid, e.sid, q.prompt, q.functionName, e.submissionText, q.constraintName, e.maxPoints, q.firstTestCase, q.firstOutput, q.secondTestCase, q.secondOutput, q.thirdTestCase, q.thirdOutput, q.fourthTestCase,
	    q.fourthOutput, q.fifthTestCase, q.fifthOutput, q.sixthTestCase, q.sixthOutput
            FROM EXAM e, QUESTION q
            WHERE e.qid=q.id AND e.id='%s';", $_GET['id']);
            //var_dump($sql);
	    $result = $conn->query($sql);
            $examInfoList = array();
            /* For every student in the class */
            while(($e = $result->fetch_assoc()) != NULL)
                array_push($examInfoList, (object)$e);
            echo json_encode($examInfoList);
        }
        else if(!empty($_POST['content']))
        {
            /* updating the exams with the new graded content */
            $examID = $_POST['id'];
            $content = json_decode($_POST['content']);
	    // echo 'Hi';
	    //var_dump($content);
            /* BEGIN TRANSACTION */
            $conn->autocommit(FALSE);
            $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

            foreach ($content as $entry) {
                $entry = json_decode($entry);
                $fb->{'name'} = $entry->{'name'};
                $fb->{'colon'} = $entry->{'colon'};
                $fb->{'constraintName'} = $entry->{'constraintName'};
                $fb->{'tests'} = $entry->{'tests'};

                // escape single quotes in the json
                $autoFeedback = json_encode($fb);
                $autoFeedback = str_replace("'", "\\'", $autoFeedback);

                $testPointsLost = 0;
                foreach($entry->{'tests'} as $tResult)
                    $testPointsLost += $tResult->{'lost'};
                $pointsLost = $testPointsLost + $entry->{'name'} + $entry->{'colon'} + $entry->{'constraintName'};
                //var_dump($entry->{'autoFeedback'}->{'pointsLost'});
                //var_dump($pointsLost);
                $sql = sprintf("UPDATE EXAM SET status=2, autoFeedback='%s', pointsReceived='%s' WHERE id='%s' AND qid='%s' AND sid='%s'",
                $autoFeedback, $entry->{'maxPoints'} - $pointsLost, $examID, $entry->{'qid'}, $entry->{'sid'});
                //var_dump($sql);
                $conn->query($sql);
            }
            
            $result = $conn->commit();
            echo ($result === true) ? "success" : "failure";
        }
    }
    else if($data == "exams")
    {
        if(!empty($_GET['id'])) 
	    {
            //var_dump($_GET['type']);
	        /* requesting all the exam info for a particular exam for review */
            if($_GET['type'] == "instructor") 
	        {
	    	    $sql = sprintf("SELECT e.name, e.qid, e.sid, q.constraintName, q.prompt, q.functionName, q.firstTestCase, q.firstOutput, q.secondTestCase, q.secondOutput, q.thirdTestCase, q.thirdOutput, q.fourthTestCase, q.fourthOutput, q.fifthTestCase, q.fifthOutput, q.sixthTestCase, q.sixthOutput, e.submissionText, e.autoFeedback, e.instructorFeedback,  e.maxPoints, e.pointsReceived 
                FROM EXAM e, QUESTION q 
                WHERE e.qid = q.id AND e.id = '%s' 
                ORDER BY e.sid", $_GET['id']);
	        } 
            else 
            {
                $sql = sprintf("SELECT e.name, e.qid, e.sid, q.constraintName, q.prompt, q.functionName, q.firstTestCase, q.firstOutput, q.secondTestCase, q.secondOutput, q.thirdTestCase, q.thirdOutput, q.fourthTestCase, q.fourthOutput, q.fifthTestCase, q.fifthOutput, q.sixthTestCase, q.sixthOutput, e.submissionText, e.autoFeedback, e.instructorFeedback,  e.maxPoints, e.pointsReceived  
                FROM EXAM e, QUESTION q
                WHERE e.qid = q.id AND e.id = %s
                ORDER BY e.sid", $_GET['id']);
            }

            //var_dump($sql);
            $examData = array();
            $result = $conn->query($sql);
                
            while(($e = $result->fetch_assoc()) != NULL)
                array_push($examData, (object)$e);
                
            echo json_encode($examData);
        }
        else if(!empty($_POST['id']))
        {
            /* updating all exams based on professor feedback */
            $submissions = json_decode($_POST['content']);
            //var_dump($submissions);
            //$submissions[i]->{'field'}

            /* BEGIN TRANSACTION */
            $conn->autocommit(FALSE);
            $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

            foreach ($submissions as $s) {
                //var_dump($s);
                $sql = sprintf("UPDATE EXAM SET status=1, instructorFeedback='%s', pointsReceived='%s' WHERE id='%s' AND qid='%s' AND sid='%s'",
                        json_encode($s->{'instructorFeedback'}), $s->{'pointsReceived'}, $_POST['id'], $s->{'qid'}, $s->{'sid'});
                //$var_dump($sql);
		        $conn->query($sql);
            }
            
            $result = $conn->commit();
            echo ($result === true) ? "success" : "failure";
        }
    }
    else if($data == "release")
    {
        $sql = sprintf("UPDATE EXAM SET status='0' WHERE id='%s'", $_POST['id']);
        $result = $conn->query($sql);
        echo ($result === true) ? "success" : "failure";
    }
    else if($data == "review")
    {
        /* requesting specific exam info for a particular exam for student review */
        $sql = sprintf("SELECT e.name, e.qid, e.sid, q.prompt, e.submissionText, e.autoFeedback, e.instructorFeedback, e.maxPoints, e.pointsReceived FROM EXAM e, QUESTION q WHERE e.qid = q.id AND e.id = %s AND e.sid='%s'", $_GET['content'], $_GET['id']);
        //echo $sql;
        $result = $conn->query($sql);
        $examContents = array();
        while(($e = $result->fetch_assoc()) != NULL)
            array_push($examContents, (object)$e);
        echo json_encode($examContents);
    }
    else if($data == "take")
    {
        /* requesting specific exam info for a particular exam for review */
        $sql = sprintf("SELECT e.name, e.qid, e.sid, e.maxPoints, q.prompt FROM EXAM e, QUESTION q WHERE e.qid=q.id AND e.id=%s AND e.sid='%s'", $_GET['content'], $_GET['id']);
       // var_dump($sql);
        $result = $conn->query($sql);
        $questionContents = array();
        while(($q = $result->fetch_assoc()) != NULL)
            array_push($questionContents, (object)$q);
        //var_dump($questionContents);
	echo json_encode($questionContents);
        
    }
    else if($data == "submit")
    {
        /* student submits exam */
        /* UPDATE each question from content with their submission, and set the status to awaiting grading (3) */
        $examID = $_POST['id'];
        //$content = json_decode($_POST['content']);
	$contents = file_get_contents("php://input");
	$content = json_decode(strstr($contents, "["));

	//var_dump($content);
	//var_dump($examID);

        /* BEGIN TRANSACTION */
        $conn->autocommit(FALSE);
        $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

        //var_dump($content);
        foreach ($content as $entry) {
            //var_dump($entry);
            $sql = sprintf("UPDATE EXAM SET status=3, submissionText='%s' WHERE id=%s AND qid='%s' AND sid='%s'",
            $entry->{'submissionText'}, $_POST['id'], $entry->{'qid'}, $entry->{'sid'});
            $conn->query($sql);
            //var_dump($sql);
        }

        $result = $conn->commit();
        echo ($result === true) ? "success" : "failure";
    }

    curl_close($ch);
?>