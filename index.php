<?php
// index.php - Main entry point for the USSD application

// Include necessary files
require_once 'config.php';
require_once 'database.php';
require_once 'ussd_handler.php';
require_once 'sms_handler.php';
require_once 'payment_handler.php';

// Initialize the database connection
$db = new Database();
$conn = $db->getConnection();

// Initialize the USSD handler
$ussdHandler = new UssdHandler($conn);

// Process the USSD request
header('Content-type: text/plain');

// Get the POST data from Africa's Talking
$sessionId = $_POST['sessionId'] ?? '';
$serviceCode = $_POST['serviceCode'] ?? '';
$phoneNumber = $_POST['phoneNumber'] ?? '';
$text = $_POST['text'] ?? '';

// Handle the USSD request
$response = $ussdHandler->handleRequest($sessionId, $serviceCode, $phoneNumber, $text);

// Output the response
echo $response;