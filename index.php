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


/* ================= RECEIVE PAYLOAD ================= */

$payload = file_get_contents("php://input");

$time = date("Y-m-d H:i:s");


/* ================= BUILD MESSAGE ================= */

if(!$payload || trim($payload) === ""){

    $finalMessage = "⚠️ EMPTY PAYLOAD\n\n🕒 ".$time;

} else {


    $data = json_decode($payload, true);


    if(json_last_error() === JSON_ERROR_NONE){


        // ONLY TAKE THE MESSAGE SENT FROM PING2

        if(isset($data['message'])){

            $finalMessage = $data['message'];

        } else {

            $finalMessage = json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

        }


    } else {


        $finalMessage = $payload;

    }

}



/* ================= TELEGRAM FORMAT ================= */

// Force monospace table view

$telegramMessage = "```\n";
$telegramMessage .= $finalMessage;
$telegramMessage .= "\n```";



/* ================= SEND TO ALL BOTS ================= */

foreach($bots as $bot){


    $url = "https://api.telegram.org/bot".$bot['token']."/sendMessage";


    $params = [

        "chat_id" => $bot['chat_id'],

        "text" => $telegramMessage,

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

    }


    curl_close($ch);

}



/* ================= RESPONSE ================= */

echo json_encode([

    "status" => "received",

    "time" => $time

]);

?>
