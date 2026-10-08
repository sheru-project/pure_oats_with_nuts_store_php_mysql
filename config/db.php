<?php
// ============ ERROR REPORTING (Development) ============
// Production mein yeh 2 lines comment kar dein
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============ SESSION ============
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ============ DATABASE ============
$host = "localhost";
$user = "root";        // Apna DB username
$pass = "";            // Apna DB password
$dbname = "oats_store"; // Apna DB name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// ============ SITE CONSTANTS ============
define('SITE_NAME', 'Pure Oats with Nuts');
define('SITE_URL', 'https://yourdomain.com'); // Apna domain
define('WHATSAPP_NUMBER', '923113138188');    // Apna WhatsApp (without +)
define('CONTACT_PHONE', '+923113138188');
define('CONTACT_EMAIL', 'info@pureoats.pk');
define('EASYPAISA_NAME', 'Yasrab');
define('EASYPAISA_NUMBER', '03198983346');
define('JAZZCASH_NAME', 'Yasrab');
define('JAZZCASH_NUMBER', '03198983346');
define('FREE_SHIPPING_MIN', 2000);
define('SHIPPING_FEE', 200);
define('ORDER_NOTIFICATION_EMAIL', 'codewithsheru@gmail.com');

function getShippingOptions(): array
{
    return [
        'Hyderabad' => 200,
        'Karachi' => 250,
        'Lahore' => 300,
        'Islamabad' => 350,
        'Rawalpindi' => 350,
        'Multan' => 300,
        'Faisalabad' => 300,
        'Peshawar' => 350,
        'Quetta' => 400,
        'Other city' => 350,
    ];
}

function getShippingCharge(string $location): int
{
    $location = trim($location);
    if($location === '') {
        return SHIPPING_FEE;
    }

    $rates = getShippingOptions();
    foreach($rates as $city => $fee) {
        if(strcasecmp($location, $city) === 0) {
            return (int)$fee;
        }
        if(stripos($location, $city) !== false) {
            return (int)$fee;
        }
    }

    return (int)$rates['Other city'];
}

function getHyderabadLocationAliases(): array
{
    return [
        'Hyderabad',
        'Latifabad',
        'Qasimabad',
        'Hirabad',
        'Hyderabad Cantt',
        'Hyderabad Cantonment',
        'Saddar Hyderabad',
        'Auto Bhan Road',
        'Citizen Colony',
        'Gulistan-e-Sajjad',
        'Wadhu Wah Road',
        'Thandi Sarak',
        'Paretabad',
        'Hala Naka',
    ];
}

function isCodAvailable(string $location, string $address = ''): bool
{
    $location = strtolower(trim($location));
    $address = strtolower(trim($address));

    foreach(getShippingOptions() as $city => $fee) {
        if($city !== 'Hyderabad' && $city !== 'Other city'
            && preg_match('/(?:^|[^a-z0-9])' . preg_quote(strtolower($city), '/') . '(?:$|[^a-z0-9])/', $location)) {
            return false;
        }
    }

    $combined_location = $location . ' ' . $address;
    foreach(getHyderabadLocationAliases() as $alias) {
        if(preg_match('/(?:^|[^a-z0-9])' . preg_quote(strtolower($alias), '/') . '(?:$|[^a-z0-9])/', $combined_location)) {
            return true;
        }
    }

    return false;
}
?>