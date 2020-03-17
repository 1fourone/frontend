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
    
    $conn->close();
?>