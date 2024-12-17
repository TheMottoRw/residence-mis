<?php
function sendRequest($arr){
    // API URL of the Flask application
    $url = 'http://localhost:5000/sendemail';

// Data to be sent in the POST request
    $data = array(
        'subject' => $arr['subject'],
        'to' => $arr['to'],
        'email' => $arr['body'],
    );

// Encode the data as JSON
    $json_data = json_encode($data);

// Initialize cURL
    $ch = curl_init($url);

// Set cURL options
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  // Return response as a string
    curl_setopt($ch, CURLOPT_POST, true);  // Use POST method
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);  // Attach JSON data
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',  // Tell Flask server to expect JSON
        'Content-Length: ' . strlen($json_data)
    ));

// Execute the cURL request
    $response = curl_exec($ch);
    echo $response->message;

// Check for errors
//    if ($response === false) {
//        echo 'Error: ' . curl_error($ch);
//    } else {
//        // Success response
//        echo 'Server Response: ' . $response;
//    }

// Close cURL session
    curl_close($ch);
    return $response;
}
?>
