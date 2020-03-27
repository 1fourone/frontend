<?php
    //middle receives credentials through $_POST['credentials']
    $cred = $_POST['credentials'];

    //Hashes password that came in via front
    $data = json_decode($cred);
    $hashed_pw = hash('sha256', $data->{'plain_password'});

    //"modified" credentials are sent to back
    $c->{'name'} = $data->{'name'};
    $c->{'hashed_password'} = $hashed_pw;
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "http://1fourone.io/webgrader/back/login.php");
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "credentials=" . json_encode($c));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $output = curl_exec($ch);
    curl_close($ch);

    if($output === false)
        echo 'Curl error: ' . curl_error($ch);
    else
        echo $output;
?>