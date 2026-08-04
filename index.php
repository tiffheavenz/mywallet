<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* ================= SECURITY ================= */

$secret = "MY_SECRET_KEY";

if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    exit("Unauthorized");
}

/* ================= TELEGRAM (MULTIPLE BOTS) ================= */

$bots = [
    [
        "token" => "8896732586:AAG2boPOp7mteDed11I2j7PYRn6L-Ln-3vQ",
        "chat"  => "8940716704"
    ],
    [
        "token" => "8880567979:AAEh_kpBSs7YzAYqLO_G6ZUqF-6m0nQJmWs",
        "chat"  => "8938414761"
    ]
];

/* ================= FUNCTION ================= */

function sendToAllBots($bots, $text) {
    foreach ($bots as $bot) {
        @file_get_contents(
            "https://api.telegram.org/bot{$bot['token']}/sendMessage?" .
            http_build_query([
                "chat_id" => $bot['chat'],
                "text" => $text
            ])
        );
    }
}

/* ================= DB CONNECTION ================= */

try {
    $pdo = new PDO(
        "pgsql:host=ep-blue-sound-ayd48i6r.c-5.us-east-2.aws.neon.tech;port=5432;dbname=neondb;sslmode=require",
        "neondb_owner",
        "npg_v5ZXNhkD7Han",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    sendToAllBots($bots, "❌ DB CONNECTION FAILED\n" . $e->getMessage());
    exit("DB ERROR");
}

/* ================= RECEIVE PAYLOAD ================= */

$payload = file_get_contents("php://input");

if (!$payload || empty(trim($payload))) {
    exit("No payload");
}

/* ================= DECODE JSON ================= */

$data = json_decode($payload, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    sendToAllBots($bots, "❌ INVALID JSON\n\n" . $payload);
    exit("Invalid JSON");
}

/* ================= EXTRACT VALUES ================= */

$status    = strtoupper(trim($data['status'] ?? 'UNKNOWN'));
$reference = trim($data['customer_reference'] ?? 'N/A');
$number    = trim($data['msisdn'] ?? 'N/A');
$amount    = (float)($data['amount'] ?? 0);
$provider  = trim($data['provider'] ?? 'N/A');
$msg       = trim($data['message'] ?? '');
$time      = trim($data['completed_at'] ?? date("Y-m-d H:i:s"));

/* ================= FORMAT MESSAGE ================= */

$title = ($status === "SUCCESS")
    ? "✅ PAYMENT SUCCESS"
    : "❌ PAYMENT FAILED";

$message  = "{$title}\n\n";
$message .= "📌 Ref: {$reference}\n";
$message .= "📱 Number: {$number}\n";
$message .= "💰 Amount: UGX " . number_format($amount) . "\n";
$message .= "🏦 Provider: {$provider}\n";
$message .= "📝 Msg: {$msg}\n";
$message .= "🕒 Time: {$time}";

/* ================= SEND TO ALL BOTS ================= */

sendToAllBots($bots, $message);

/* ================= STORE ONLY SUCCESS ================= */

if ($status !== "SUCCESS") {
    echo "IGNORED";
    exit;
}

try {

    $stmt = $pdo->prepare("
        INSERT INTO transactions
        (reference, status, amount, msisdn, provider, message, completed_at)
        VALUES
        (:reference, :status, :amount, :msisdn, :provider, :message, :completed_at)
        ON CONFLICT (reference)
        DO UPDATE SET
            status = EXCLUDED.status,
            amount = EXCLUDED.amount,
            msisdn = EXCLUDED.msisdn,
            provider = EXCLUDED.provider,
            message = EXCLUDED.message,
            completed_at = EXCLUDED.completed_at
    ");

    $stmt->execute([
        ":reference"    => $reference,
        ":status"       => $status,
        ":amount"       => $amount,
        ":msisdn"       => $number,
        ":provider"     => $provider,
        ":message"      => $msg,
        ":completed_at" => $time
    ]);

    echo "SUCCESS STORED";

} catch (PDOException $e) {
    sendToAllBots($bots, "❌ DB INSERT ERROR\n" . $e->getMessage());
    echo "DB ERROR";
}
