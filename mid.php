<?php
    //should be able to access _POST['credentials'] to get credentials sent by front
    //password isn't hashed - mid's responsibility to hash it
    //example file for mid/back to see flow
    $v = $_POST['credentials'];

    //echo $v;

    // ... some processing
    //hash password, and prepare the "hashed credentials" to send
    $data = json_decode($v);
    $pw_hashed = hash('sha256', $data->{'password'});
    $hashed_data = $data;
    $hashed_data->{'password'} = $pw_hashed;
    $h = json_encode($hashed_data);

    //talk to back.php via cURL and send a POST request with the (updated) credentials JSON string
    //and receive a result JSON string
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, "https://web.njit.edu/~ml637/testing/back.php"); //@TODO: update with correct middle php path
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "credentials=" . $h);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    $output = curl_exec($ch);

    if($output === false)
        echo 'Curl error: ' . curl_error($ch);
    else {
        //back's result is here, in $output
        $result = json_decode($output);
        //@TODO: spoof NJIT here
        $spoof_result = "failure";
        $result->{'njit'} = $spoof_result;
        echo json_encode($result);
    }
    
    curl_close($ch);
?>