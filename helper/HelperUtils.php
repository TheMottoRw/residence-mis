<?php
function generateRandomString($length = 6) {
// Define the characters that will be used for the random string
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$charactersLength = strlen($characters);
$randomString = '';

// Generate a random string of specified length
for ($i = 0; $i < $length; $i++) {
$randomString .= $characters[rand(0, $charactersLength - 1)];
}

return $randomString;
}
?>