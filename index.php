<?php

ini_set('display_errors',1);
error_reporting(E_ALL);

header("Content-Type: application/json");


$botToken = "8677498486:AAFyHSstosvrtaBJwj-_eV25U3eWKkbKwOo";
$chatId   = "8940716704";


function sendTelegram($message)
{
    global $botToken, $chatId;


    $url = "https://api.telegram.org/bot".$botToken."/sendMessage";


    $data = [
        "chat_id"=>$chatId,
        "text"=>$message
    ];


    $ch = curl_init($url);


    curl_setopt_array($ch,[

        CURLOPT_POST=>true,

        CURLOPT_POSTFIELDS=>http_build_query($data),

        CURLOPT_RETURNTRANSFER=>true

    ]);


    $response = curl_exec($ch);


    if(curl_errno($ch)){
        file_put_contents(
            "telegram_error.txt",
            curl_error($ch)
        );
    }


    curl_close($ch);


    file_put_contents(
        "telegram_response.txt",
        $response
    );


    return $response;
}



$raw = file_get_contents("php://input");



if(!$raw){

    echo json_encode([
        "status"=>"no data"
    ]);

    exit;
}



$data = json_decode($raw,true);



$message = "🔥 SHJEEEE WALLET REPORT\n\n";


if(isset($data['users'])){


    foreach($data['users'] as $user){

        $message .=
        "👤 ".$user['name']."\n".
        "📱 ".$user['phone']."\n".
        "💰 UGX ".$user['wallet']."\n".
        "----------------\n";

    }


    $message .= "\n🔥 TOTAL WALLET\n";
    $message .= "UGX ".$data['total_wallet'];

}


$result = sendTelegram($message);



echo json_encode([
    "status"=>"sent",
    "telegram"=>$result
]);

?>
