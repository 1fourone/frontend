<?php

    //Connect to the database
    $dbServerName = "1fourone.io:3306";
    $dbUser = "webster";
    $dbPassword = "490project";
    $dbName = "webgrader";
    $conn = mysqli_connect($dbServerName, $dbUser, $dbPassword, $dbName);
    
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $req = $_REQUEST["value"];

    //Caller wants the question bank
    if($req == "qbank")
    {
        $questions = array();
        //Query the database for a valid student/instructor id, and respond appropriately
        $sql = "SELECT * FROM QUESTION;";
        $result = $conn->query($sql);
        while(($q = $result->fetch_assoc()) != NULL)
            array_push($questions, (object)$q);
        //var_dump($questions);
        //var_dump(json_encode($questions));
        //var_dump(json_encode($questions[0]));
        echo json_encode($questions);
    }
    //Caller wants the exams (for displaying in homepage)
    else if($req == "exams")
    {
        if(isset($_REQUEST['student'])) //Student requested this
        {
            //first get the active exams
            $sql = "SELECT DISTINCT e.id, e.name, c.course, c.section FROM EXAM e, CLASS c WHERE e.sid='" . $_REQUEST['student'] . "' AND e.cid=c.id AND status=2";
            $result = $conn->query($sql);
            $actives = array();
            while(($e = $result->fetch_assoc()) != NULL)
                array_push($actives, (object)$e);
            //echo json_encode($actives);

            //then get the unreleased exams
            $sql = "SELECT DISTINCT e.id, e.name, c.course, c.section FROM EXAM e, CLASS c WHERE e.sid='" . $_REQUEST['student'] . "' AND e.cid=c.id AND status=1";
            $result = $conn->query($sql);
            $unreleased = array();
            while(($e = $result->fetch_assoc()) != NULL)
                array_push($unreleased, (object)$e);
            
            //then get the released exams
            $sql = "SELECT DISTINCT e.id, e.name, c.course, c.section FROM EXAM e, CLASS c WHERE e.sid='" . $_REQUEST['student'] . "' AND e.cid=c.id AND status=0";
            $result = $conn->query($sql);
            $released = array();
            while(($e = $result->fetch_assoc()) != NULL)
                array_push($released, (object)$e);

            $all = array($actives, $released, $unreleased);
            echo json_encode($all);
        }
        else //Instructor requested this
        {
            echo "Instructor " . $_REQUEST['instructor'] . " requested exams.";
        }
    }
    //Student wants an exam's questions/data for taking exam
    else if($req == "exam")
    {
        $sql = "SELECT q.prompt, q.id, e.maxPoints FROM EXAM e, QUESTION q WHERE e.qid=q.id AND e.id='" . $_REQUEST['id'] . "' AND e.sid='" . $_REQUEST['student'] . "'";
        $result = $conn->query($sql);
        $questions = array();
        while(($q = $result->fetch_assoc()) != NULL)
            array_push($questions, (object)$q);
        echo json_encode($questions);
    }
    
    $conn->close();
?>