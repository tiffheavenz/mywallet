<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ================= TELEGRAM ================= */

// Multiple bots (token + chat_id)
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

if (!$payload || empty(trim($payload))) {
    exit("❌ No payload received");
}

/* ================= DECODE JSON ================= */

$data = json_decode($payload, true);

if (json_last_error() !== JSON_ERROR_NONE) {

    $errorMessage = "❌ INVALID JSON RECEIVED\n\n" . $payload;

    // Send error to ALL bots
    foreach ($bots as $bot) {
        file_get_contents(
            "https://api.telegram.org/bot{$bot['token']}/sendMessage?" .
            http_build_query([
                "chat_id" => $bot['chat_id'],
                "text" => $errorMessage
            ])
        );
    }

    exit("Invalid JSON");
}
/* ================= VALUES ================= */

$userId    = $data['user_id'] ?? 'N/A';
$name      = $data['name'] ?? 'N/A';
$amount    = (float)($data['amount'] ?? 0);
$fee       = (float)($data['fee'] ?? 0);
$netAmount = (float)($data['net_amount'] ?? 0);
$reference = $data['reference'] ?? 'N/A';
$status    = strtoupper($data['status'] ?? 'PENDING');
$time      = date("Y-m-d H:i:s");

/* ================= TELEGRAM MESSAGE ================= */

$message  = "💸 WITHDRAWAL REQUEST\n\n";
$message .= "👤 User ID: ".$userId."\n";
$message .= "🧑 Name: ".$name."\n";
$message .= "💰 Amount: UGX ".number_format($amount)."\n";
$message .= "💸 Fee: UGX ".number_format($fee)."\n";
$message .= "✅ Net Amount: UGX ".number_format($netAmount)."\n";
$message .= "📌 Reference: ".$reference."\n";
$message .= "📋 Status: ".$status."\n";
$message .= "🕒 Time: ".$time;

/* ================= SEND TO TELEGRAM ================= */

file_get_contents(
    "https://api.telegram.org/bot{$botToken}/sendMessage?" .
    http_build_query([
        "chat_id" => $chatId,
        "text" => $message
    ])
);

echo "WITHDRAWAL RECEIVED";
/* ================= SEND TO WHATSAPP (CALLMEBOT) ================= */

$whatsappPhone = "256755336031";
$whatsappApiKey = "5893046";

$whatsappMessage = urlencode($message);

$whatsappUrl = "https://api.callmebot.com/whatsapp.php?" . http_build_query([
    "phone" => $whatsappPhone,
    "text" => $message,
    "apikey" => $whatsappApiKey
]);

file_get_contents($whatsappUrl);
?>
