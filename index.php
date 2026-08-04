<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


$bots = [

    [
        "token" => "8677498486:AAFyHSstosvrtaBJwj-_eV25U3eWKkbKwOo",
        "chat_id" => "8940716704"
    ]

];



function sendTelegram($bots, $message)
{

    foreach($bots as $bot){

        file_get_contents(
            "https://api.telegram.org/bot".$bot['token']."/sendMessage?".
            http_build_query([
                "chat_id"=>$bot['chat_id'],
                "text"=>$message
            ])
        );

    }

}



$raw = file_get_contents("php://input");



if(!$raw){

    echo "NO DATA RECEIVED";
    exit;

}



// SAVE EVERYTHING RECEIVED
file_put_contents(
    "received.txt",
    date("Y-m-d H:i:s")."\n".$raw."\n\n",
    FILE_APPEND
);



$data = json_decode($raw,true);



$message = "🔥 RENDER RECEIVED\n\n";


if($data){

    $message .= json_encode(
        $data,
        JSON_PRETTY_PRINT
    );

}else{

    $message .= $raw;

}



sendTelegram(
    $bots,
    $message
);



echo "FORWARDED";

?>
