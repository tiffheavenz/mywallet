<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


/* ================= TELEGRAM BOTS ================= */

$bots = [

    [
        "token" => "8677498486:AAFyHSstosvrtaBJwj-_eV25U3eWKkbKwOo",
        "chat_id" => "8940716704"
    ],

    [
        "token" => "8880567979:AAEh_kpBSs7YzAYqLO_G6ZUqF-6m0nQJmWs",
        "chat_id" => "8938414761"
    ]

];



/* ================= RECEIVE PAYLOAD ================= */

$payload = file_get_contents("php://input");


// STOP EMPTY LOADS

if (!$payload || trim($payload) == "") {

    echo json_encode([
        "status"=>"ignored",
        "reason"=>"empty payload"
    ]);

    exit;

}




/* ================= GET MESSAGE ================= */

$data = json_decode($payload, true);


if (
    json_last_error() === JSON_ERROR_NONE &&
    isset($data['message'])
) {

    $message = trim($data['message']);

} else {

    $message = trim($payload);

}



/* ================= CLEAN MESSAGE ================= */

$message = str_replace(
    [
        "🔥 RENDER MESSAGE RECEIVED",
        "🕒 TIME:",
        "📦 JSON DATA:",
        "```"
    ],
    "",
    $message
);


$message = trim($message);



// STOP IF MESSAGE IS EMPTY AFTER CLEANING

if ($message == "") {

    echo json_encode([
        "status"=>"ignored",
        "reason"=>"empty message"
    ]);

    exit;

}




/* ================= SEND TELEGRAM ================= */

foreach ($bots as $bot) {


    $url = 
    "https://api.telegram.org/bot".
    $bot['token'].
    "/sendMessage";


    $params = [

        "chat_id" => $bot['chat_id'],

        "text" => "```\n".$message."\n```",

        "parse_mode" => "Markdown"

    ];



    $ch = curl_init($url);


    curl_setopt_array($ch,[

        CURLOPT_POST => true,

        CURLOPT_RETURNTRANSFER => true,

        CURLOPT_POSTFIELDS => http_build_query($params),

        CURLOPT_TIMEOUT => 10

    ]);



    $response = curl_exec($ch);



    if(curl_errno($ch)){

        error_log(
            "TELEGRAM ERROR ".$bot['chat_id']." : ".
            curl_error($ch)
        );

    } else {

        error_log(
            "TELEGRAM RESPONSE ".$bot['chat_id']." : ".$response
        );

    }



    curl_close($ch);

}




/* ================= RESPONSE ================= */

echo json_encode([

    "status"=>"sent",

    "message"=>$message

]);

?>
