<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);


/* ================= TELEGRAM BOTS ================= */

$bots = [

    [
        "token" => "YOUR_BOT_TOKEN_1",
        "chat_id" => "YOUR_CHAT_ID_1"
    ],

    [
        "token" => "8880567979:AAEh_kpBSs7YzAYqLO_G6ZUqF-6m0nQJmWs",
        "chat_id" => "8938414761"
    ]

];



/* ================= SEND TELEGRAM FUNCTION ================= */

function sendTelegramToAll($bots, $message)
{

    foreach ($bots as $bot) {

        file_get_contents(
            "https://api.telegram.org/bot{$bot['token']}/sendMessage?" .
            http_build_query([
                "chat_id" => $bot['chat_id'],
                "text" => $message
            ])
        );

    }

}



/* ================= RECEIVE REQUEST ================= */

$payload = file_get_contents("php://input");


if (!$payload || empty(trim($payload))) {

    exit("NO DATA");

}



/* ================= DECODE JSON ================= */

$data = json_decode($payload, true);


if (json_last_error() !== JSON_ERROR_NONE) {


    sendTelegramToAll(
        $bots,
        "❌ INVALID JSON\n\n".$payload
    );


    exit("INVALID JSON");

}



/* ================= WALLET REPORT ================= */


if (($data['type'] ?? '') === "wallet_report") {


    $users = $data['users'] ?? [];


    if (empty($users)) {

        exit("NO USERS");

    }



    $totalWallet = 0;



    $message = "💰 WALLET BALANCE REPORT\n\n";



    foreach ($users as $user) {


        $wallet = (float)($user['wallet'] ?? 0);


        $totalWallet += $wallet;



        $message .= "👤 Name: ".($user['name'] ?? 'N/A')."\n";

        $message .= "🆔 User ID: ".($user['id'] ?? 'N/A')."\n";

        $message .= "📱 Phone: +256".($user['phone'] ?? 'N/A')."\n";

        $message .= "💵 Wallet: UGX ".number_format($wallet)."\n";

        $message .= "----------------------\n";


    }



    $message .= "\n🔥 TOTAL WALLET BALANCE\n";

    $message .= "💰 UGX ".number_format($totalWallet)."\n";

    $message .= "🕒 ".date("Y-m-d H:i:s");



    // Send Telegram

    sendTelegramToAll($bots, $message);



    // Send WhatsApp

    $whatsappPhone = "256755336031";

    $whatsappApiKey = "5893046";


    file_get_contents(

        "https://api.callmebot.com/whatsapp.php?" .

        http_build_query([

            "phone" => $whatsappPhone,

            "text" => $message,

            "apikey" => $whatsappApiKey

        ])

    );



    echo "WALLET REPORT SENT";


    exit;

}



/* ================= UNKNOWN REQUEST ================= */


sendTelegramToAll(
    $bots,
    "⚠️ UNKNOWN REQUEST RECEIVED\n\n".$payload
);


echo "UNKNOWN REQUEST";

?>
