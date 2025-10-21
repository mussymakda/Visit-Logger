<?php

/**
 * API Test Script
 * 
 * Test the Designer API endpoints to ensure everything is working locally.
 * Run this script with: php test-api.php
 */

$baseUrl = 'http://127.0.0.1:8000/api/designer';

echo "🚀 Testing Visit Logger API\n";
echo "==========================\n\n";

// Test 1: Login
echo "1. Testing Login...\n";
$loginData = [
    'email' => 'test@example.com',
    'password' => 'password'
];

$ch = curl_init($baseUrl . '/login');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$loginResult = json_decode($response, true);

if ($httpCode === 200 && isset($loginResult['success']) && $loginResult['success']) {
    $token = $loginResult['data']['token'];
    echo "✅ Login successful! Token: " . substr($token, 0, 20) . "...\n\n";
} else {
    echo "❌ Login failed: " . ($loginResult['message'] ?? 'Unknown error') . "\n";
    echo "Response: " . $response . "\n\n";
    exit(1);
}

// Test 2: Profile
echo "2. Testing Profile...\n";
$ch = curl_init($baseUrl . '/profile');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$profileResult = json_decode($response, true);

if ($httpCode === 200 && isset($profileResult['success']) && $profileResult['success']) {
    $user = $profileResult['data']['user'];
    echo "✅ Profile retrieved! User: {$user['name']} ({$user['email']})\n\n";
} else {
    echo "❌ Profile failed: " . ($profileResult['message'] ?? 'Unknown error') . "\n\n";
}

// Test 3: QR Verification
echo "3. Testing QR Verification...\n";
$qrData = ['qr_data' => 'SPONSOR-1'];

$ch = curl_init($baseUrl . '/verify-qr');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($qrData));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$qrResult = json_decode($response, true);

if ($httpCode === 200 && isset($qrResult['success']) && $qrResult['success']) {
    $sponsor = $qrResult['data']['sponsor'];
    echo "✅ QR Verification successful! Sponsor: {$sponsor['name']} (ID: {$sponsor['id']})\n\n";
} else {
    echo "❌ QR Verification failed: " . ($qrResult['message'] ?? 'Unknown error') . "\n\n";
}

// Test 4: Statistics
echo "4. Testing Statistics...\n";
$ch = curl_init($baseUrl . '/stats');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$statsResult = json_decode($response, true);

if ($httpCode === 200 && isset($statsResult['success']) && $statsResult['success']) {
    $stats = $statsResult['data']['stats'];
    echo "✅ Statistics retrieved!\n";
    echo "   - Total visits: {$stats['total_visits']}\n";
    echo "   - Visits today: {$stats['visits_today']}\n";
    echo "   - Unique sponsors: {$stats['unique_sponsors']}\n\n";
} else {
    echo "❌ Statistics failed: " . ($statsResult['message'] ?? 'Unknown error') . "\n\n";
}

// Test 5: Visit History
echo "5. Testing Visit History...\n";
$ch = curl_init($baseUrl . '/visits?page=1&limit=5');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $token,
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$visitsResult = json_decode($response, true);

if ($httpCode === 200 && isset($visitsResult['success']) && $visitsResult['success']) {
    $visits = $visitsResult['data']['visits'];
    $pagination = $visitsResult['data']['pagination'];
    echo "✅ Visit history retrieved! Found {$pagination['total']} total visits\n";
    echo "   Current page: {$pagination['current_page']}/{$pagination['last_page']}\n\n";
} else {
    echo "❌ Visit history failed: " . ($visitsResult['message'] ?? 'Unknown error') . "\n\n";
}

echo "🎉 API Testing Complete!\n";
echo "========================\n";
echo "Your API is ready for Flutter integration at: $baseUrl\n";
echo "\n📋 Next Steps:\n";
echo "1. Use the test credentials in your Flutter app\n";
echo "2. Update the base URL when deploying to production\n";
echo "3. Test photo upload functionality with actual images\n";