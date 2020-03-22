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

    curl_close($ch);
?>