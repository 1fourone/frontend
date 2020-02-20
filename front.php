<?php
    //json credentials string is now available through $_POST['credentials']
    // DON't hash password, send plaintext credentials to mid
    $v = $_POST['credentials'];

    //talk to mid.php via cURL and send a POST request with the (updated) credentials JSON string
    //and receive a result JSON string
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://web.njit.edu/~as2863/middle.php"); //@TODO: update with correct middle php path
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