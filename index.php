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


if (!$payload || trim($payload) == "") {

    $message = "⚠️ EMPTY PAYLOAD";

} else {


    $data = json_decode($payload, true);


    if (json_last_error() === JSON_ERROR_NONE && isset($data['message'])) {

        // ONLY GET MESSAGE FROM PING2
        $message = trim($data['message']);

    } else {

        // IF RAW TEXT
        $message = trim($payload);

    }

}



/* ================= CLEAN MESSAGE ================= */

// Remove unwanted previous headers if they exist
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



/* ================= SEND TELEGRAM ================= */

foreach ($bots as $bot) {


    $url = "https://api.telegram.org/bot".$bot['token']."/sendMessage";


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


    curl_exec($ch);


    curl_close($ch);

}



/* ================= RESPONSE ================= */

echo json_encode([

    "status"=>"sent",

    "message"=>$message

]);

?>
