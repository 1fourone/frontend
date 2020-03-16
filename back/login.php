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
    
    //Get credentials with hashed password
    $cred = $_POST['credentials'];
    $cred = json_decode($cred);
    $name = $cred->{'name'};
    $pw = $cred->{'hashed_password'};

    
    //Query the database for a valid student/instructor id, and respond appropriately
    $sql = "SELECT sid, iid FROM USER WHERE name='$name' AND password='$pw'";
    $result = $conn->query($sql);

    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if(empty($row['sid'])) 
            $r->{'type'} = "instructor";
        else
            $r->{'type'} = "student";
        $r->{'result'} = "success";
    }
    else {
        $r->{'type'} = "";
        $r->{'result'} = "failure";
    }

    $conn->close();
    echo json_encode($r);

?>