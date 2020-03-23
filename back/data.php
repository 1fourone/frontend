<?php

    /* Prepare SQL connection */
    $dbServerName = "1fourone.io:3306";
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

            $examList = array();
            $sql = sprintf("SELECT DISTINCT e.id, e.name, e.date, e.status, c.course, c.section FROM EXAM e, CLASS c WHERE e.sid='%s' AND c.id=e.cid;", $_GET['student']);
            $result = $conn->query($sql);
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
            $question = json_decode($_POST['question']);
            $sql = sprintf("INSERT INTO QUESTION(id, prompt, functionSignature, difficulty, topic, creatorID, firstTestCase, firstOutput, secondTestCase, secondOutput) VALUES(UUID(), '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
                $question->{'prompt'}, $question->{'functionSignature'}, $question->{'difficulty'}, $question->{'topic'}, $question->{'creatorID'}, $question->{'firstTestCase'}, 
                $question->{'firstOutput'}, $question->{'secondTestCase'}, $question->{'secondOutput'});
            $result = $conn->query($sql);
            echo ($result === false) ? "failure" : "success";
        }
    }
    else if($data == "bank")
    {
        /* getting all questions from the bank */
        $questions = array();
        $sql = "SELECT q.id, q.prompt, q.difficulty, q.topic, i.uname AS creatorName, q.creationDate FROM QUESTION q, INSTRUCTOR i WHERE q.creatorID=i.id;";
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

            /* TRANSACTION - insert each question to all members in class */
            $conn->autocommit(FALSE);
            $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
            
            /* INSERT INTO EXAM(id, name, qid, sid, cid, status, maxPoints) */
            for($i = 0; $i < count($examList); $i++)
            {
                for($j = 0; $j < count($students); $j++)
                {
                    $sql = sprintf("INSERT INTO EXAM(id, name, qid, sid, cid, status, maxPoints) VALUES('%s', '%s', '%s', '%s', '%s', '%s', '%s');",
                    $examID, $examList[$i]->{'name'}, $examList[$i]->{'id'}, $students[$j]['id'], $examList[$i]->{'cid'}, 4, $examList[$i]->{'maxPoints'});
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
            $sql = sprintf("SELECT e.qid, e.sid, q.prompt, q.functionSignature, e.submissionText, e.maxPoints, q.firstTestCase, q.firstOutput, q.secondTestCase, q.secondOutput
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

            /* BEGIN TRANSACTION */
            $conn->autocommit(FALSE);
            $conn->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);

            foreach ($content as $entry) {
                $pointsLost = array_sum($entry->{'autoFeedback'}->{'pointsLost'});
                //var_dump($entry->{'autoFeedback'}->{'pointsLost'});
                //var_dump($pointsLost);
                $sql = sprintf("UPDATE EXAM SET status=2, autoFeedback='%s', pointsReceived='%s' WHERE id='%s' AND qid='%s' AND sid='%s'",
                json_encode($entry->{'autoFeedback'}), $entry->{'maxPoints'} - $pointsLost, $examID, $entry->{'qid'}, $entry->{'sid'});
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
            /* requesting all the exam info for a particular exam for review */
            $sql = sprintf("SELECT e.name, e.qid, e.sid, q.prompt, e.submissionText, e.autoFeedback, e.maxPoints, e.pointsReceived FROM EXAM e, QUESTION q WHERE e.qid = q.id AND e.id = '%s'", $_GET['id']);
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
                $sql = sprintf("UPDATE EXAM SET status=1, instructorFeedback='%s', pointsReceived='%s' WHERE id='%s' AND qid='%s' AND sid='%s'",
                json_encode($s->{'instructorFeedback'}), $s->{'pointsReceived'}, $_POST['id'], $s->{'qid'}, $s->{'sid'});
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

    curl_close($ch);
?>