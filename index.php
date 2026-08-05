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
        "token" => "8565074370:AAFz_Opi7kYiAJc5ptVHhsxNzIEPAZIYUpUE",
        "chat_id" => "8938414761"
    ]

];


/* ================= RECEIVE ANYTHING ================= */

$payload = file_get_contents("php://input");

$time = date("Y-m-d H:i:s");


$message  = "🔥 RENDER MESSAGE RECEIVED\n\n";
$message .= "🕒 TIME: ".$time."\n\n";


if(!$payload || empty(trim($payload))){

    $message .= "⚠️ EMPTY PAYLOAD";

} else {

    $data = json_decode($payload, true);


    if(json_last_error() === JSON_ERROR_NONE){


        /*
        If payload contains message from ping2,
        extract it cleanly
        */

        if(isset($data['message'])){

            $message .= $data['message'];

        } else {

            $message .= json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

        }


    } else {


        $message .= $payload;

    }

}


/* ================= FORCE TABLE FORMAT ================= */

$message = "```\n".$message."\n```";



/* ================= SEND TO ALL TELEGRAM BOTS ================= */

foreach($bots as $bot){


    $url = "https://api.telegram.org/bot".$bot['token']."/sendMessage";


    $response = file_get_contents($url . "?" . http_build_query([

        "chat_id" => $bot['chat_id'],

        "text" => $message,

        "parse_mode" => "Markdown"

    ]));


    if($response === false){

        error_log("Telegram failed for ".$bot['chat_id']);

    }

}



/* ================= RESPONSE TO SENDER ================= */

echo json_encode([

    "status" => "received",

    "time" => $time,

    "payload" => $payload

]);


?>
