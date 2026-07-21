<?php
// ===== PAYSTACK PROXY =====
// Place this file in your server root folder
// Access via: https://yourdomain.com/proxy.php

// Enable CORS for all origins
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Your Paystack Secret Key (TEST MODE)
$SECRET_KEY = "sk_test_cc150f72b038be6c2839ec8e0a44a5a27d30491c";

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$accountNumber = $input['account_number'] ?? $_GET['account_number'] ?? '';
$bankCode = $input['bank_code'] ?? $_GET['bank_code'] ?? '';

// Validate input
if (empty($accountNumber) || empty($bankCode)) {
    echo json_encode([
        'status' => false,
        'message' => 'Account number and bank code are required'
    ]);
    exit();
}

// Call Paystack API
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://api.paystack.co/bank/resolve?account_number={$accountNumber}&bank_code={$bankCode}",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer {$SECRET_KEY}",
        "Cache-Control: no-cache",
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo json_encode([
        'status' => false,
        'message' => "CURL Error: " . $err
    ]);
} else {
    // Return the Paystack response
    echo $response;
}
?>