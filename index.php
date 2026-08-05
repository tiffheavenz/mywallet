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



/* ================= IGNORE EMPTY PINGS ================= */

if (!$payload || trim($payload) === "") {

    echo json_encode([
        "status" => "ignored",
        "reason" => "empty payload"
    ]);

    exit;
}



/* ================= EXTRACT MESSAGE ================= */

$data = json_decode($payload, true);


$message = "";


// JSON DATA WITH MESSAGE
if (
    json_last_error() === JSON_ERROR_NONE &&
    isset($data['message']) &&
    trim($data['message']) !== ""
) {

    $message = trim($data['message']);

}


// RAW DATA
elseif (
    json_last_error() !== JSON_ERROR_NONE &&
    trim($payload) !== ""
) {

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



/* ================= STOP EMPTY MESSAGE ================= */

if ($message === "") {

    echo json_encode([
        "status" => "ignored",
        "reason" => "empty message after cleaning"
    ]);

    exit;
}




/* ================= SEND TELEGRAM ================= */

$results = [];


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

        CURLOPT_POSTFIELDS => $params,

        CURLOPT_TIMEOUT => 15

    ]);



    $response = curl_exec($ch);



    if(curl_errno($ch)){


        $results[] = [

            "chat_id"=>$bot['chat_id'],

            "status"=>"curl_error",

            "error"=>curl_error($ch)

        ];


    } else {


        $telegram = json_decode($response,true);


        $results[] = [

            "chat_id"=>$bot['chat_id'],

            "status"=>$telegram['ok'] ?? false,

            "response"=>$telegram

        ];


    }



    curl_close($ch);

}




/* ================= RESPONSE ================= */

echo json_encode([

    "status"=>"completed",

    "message_sent"=>$message,

    "telegram_results"=>$results

], JSON_PRETTY_PRINT);

?>
