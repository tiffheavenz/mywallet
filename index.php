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


$message = "🔥 RENDER MESSAGE RECEIVED\n\n";
$message .= "🕒 TIME: ".$time."\n\n";


if(!$payload || empty(trim($payload))){

    $message .= "⚠️ EMPTY PAYLOAD";

} else {


    // TRY JSON FIRST

    $data = json_decode($payload, true);


    if(json_last_error() === JSON_ERROR_NONE){


        $message .= "📦 JSON DATA:\n\n";

        $message .= json_encode(
            $data,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );


    } else {


        // NOT JSON, SEND RAW TEXT

        $message .= "📝 RAW DATA:\n\n";

        $message .= $payload;

    }

}



/* ================= SEND TO ALL TELEGRAM BOTS ================= */

foreach($bots as $bot){


    $url = "https://api.telegram.org/bot".$bot['token']."/sendMessage";


    file_get_contents($url . "?" . http_build_query([

        "chat_id" => $bot['chat_id'],

        "text" => $message

    ]));


}



/* ================= RESPONSE TO SENDER ================= */

echo json_encode([

    "status" => "received",

    "time" => $time,

    "payload" => $payload

]);


?>
