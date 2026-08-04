<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

$token = "8677498486:AAFyHSstosvrtaBJwj-_eV25U3eWKkbKwOo";
$chat = "8940716704";


$message = "🔥 TEST FROM RENDER ".date("Y-m-d H:i:s");


$url = "https://api.telegram.org/bot".$token."/sendMessage";


$post = [
    "chat_id"=>$chat,
    "text"=>$message
];


$ch = curl_init($url);

curl_setopt_array($ch,[
    CURLOPT_POST=>true,
    CURLOPT_POSTFIELDS=>$post,
    CURLOPT_RETURNTRANSFER=>true
]);


$result = curl_exec($ch);


$error = curl_error($ch);


curl_close($ch);



echo "<pre>";

echo "TELEGRAM RESULT:\n";
print_r($result);

echo "\nCURL ERROR:\n";
print_r($error);

echo "</pre>";

?>
