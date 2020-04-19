<?php
    
    //Connect to the database
    $dbServerName = "1fourone.io";
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
    $sql = "SELECT id, sid, iid FROM USER WHERE name='$name' AND password='$pw'";
    $result = $conn->query($sql);

    
    if($result->num_rows > 0) {
        $r->{'result'} = "success";
        $row = $result->fetch_assoc();
        if(empty($row['sid'])) {
            $r->{'type'} = "instructor";
            $r->{'id'} = $row['iid'];
        }
        else {
            $r->{'type'} = "student";
            $r->{'id'} = $row['sid'];
        }
    }
    else
        $r->{'result'} = "failure";

    $conn->close();
    echo json_encode($r);
?>
