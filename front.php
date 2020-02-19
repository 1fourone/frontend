<?php
    //json credentials string is now available through $_POST['credentials']
    //hash password, repackage JSON into string, and send it to mid
    $v = $_POST['credentials'];
    $data = json_decode($v);
    $pw_hashed = hash('sha256', $data->{'password'});
    $data->{'password'} = $pw_hashed;
    $v = json_encode($data);

    //talk to mid.php via cURL and send a POST request with the (updated) credentials JSON string
    //and receive a result JSON string
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://web.njit.edu/~ml637/testing/mid.php"); //@TODO: update with correct middle php path
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "credentials=" . $v);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $output = curl_exec($ch);
    if($output === false)
        echo 'Curl error: ' . curl_error($ch);
    else {
        echo $output;
    }
    
    curl_close($ch);
?>