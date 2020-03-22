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

    curl_close($ch);
?>