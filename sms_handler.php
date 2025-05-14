<?php
// sms_handler.php - Handles SMS functionality

require_once __DIR__ . '/vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

class SmsHandler {
    private $AT;
    private $sms;

    public function __construct() {
        // Initialize Africa's Talking SDK
        $this->AT = new AfricasTalking(AT_USERNAME, AT_API_KEY);
        $this->sms = $this->AT->sms();
    }

    public function sendConfirmationSms($phoneNumber, $name, $eventName, $eventDate, $eventTime, $venue) {
        // Format the date and time
        $formattedDate = date('M d, Y', strtotime($eventDate));
        $formattedTime = date('h:i A', strtotime($eventTime));
        
        // Create the message
        $message = "Hello {$name}, your registration for {$eventName} on {$formattedDate} at {$formattedTime}, {$venue} is confirmed. Thank you!";
        
        try {
            // Format phone number to international format if needed
            if (substr($phoneNumber, 0, 1) !== '+') {
                // Add country code for Rwanda
                $phoneNumber = '+250' . substr($phoneNumber, 1);
            }
            
            // Log the SMS attempt
            if (DEBUG_MODE) {
                error_log("Attempting to send SMS to: {$phoneNumber}");
                error_log("Message content: {$message}");
                error_log("Using sender ID: " . SMS_SENDER_ID);
            }
            
            // Send the message
            $result = $this->sms->send([
                'to' => $phoneNumber,
                'message' => $message,
                'from' => SMS_SENDER_ID
            ]);
            
            if (DEBUG_MODE) {
                error_log("SMS Result: " . json_encode($result));
            }
            
            return $result;
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                error_log("SMS Error: " . $e->getMessage());
                error_log("SMS Error trace: " . $e->getTraceAsString());
            }
            return false;
        }
    }

    public function sendPaymentConfirmationSms($phoneNumber, $name, $eventName, $amount) {
        // Create the message
        $message = "Hello {$name}, your payment of {$amount} for {$eventName} has been received. Thank you!";
        
        try {
            // Format phone number to international format if needed
            if (substr($phoneNumber, 0, 1) !== '+') {
                // Add country code for Rwanda
                $phoneNumber = '+250' . substr($phoneNumber, 1);
            }
            
            // Log the SMS attempt
            if (DEBUG_MODE) {
                error_log("Attempting to send payment confirmation SMS to: {$phoneNumber}");
                error_log("Message content: {$message}");
                error_log("Using sender ID: " . SMS_SENDER_ID);
            }
            
            // Send the message
            $result = $this->sms->send([
                'to' => $phoneNumber,
                'message' => $message,
                'from' => SMS_SENDER_ID
            ]);
            
            if (DEBUG_MODE) {
                error_log("Payment SMS Result: " . json_encode($result));
            }
            
            return $result;
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                error_log("Payment SMS Error: " . $e->getMessage());
                error_log("SMS Error trace: " . $e->getTraceAsString());
            }
            return false;
        }
    }

    public function sendReminderSms($phoneNumber, $name, $eventName, $eventDate, $eventTime, $venue) {
        // Format the date and time
        $formattedDate = date('M d, Y', strtotime($eventDate));
        $formattedTime = date('h:i A', strtotime($eventTime));
        
        // Create the message
        $message = "Hello {$name}, reminder: Your event {$eventName} is tomorrow at {$formattedTime}, {$venue}. We look forward to seeing you!";
        
        try {
            // Format phone number to international format if needed
            if (substr($phoneNumber, 0, 1) !== '+') {
                // Add country code for Rwanda
                $phoneNumber = '+250' . substr($phoneNumber, 1);
            }
            
            // Log the SMS attempt
            if (DEBUG_MODE) {
                error_log("Attempting to send reminder SMS to: {$phoneNumber}");
                error_log("Message content: {$message}");
                error_log("Using sender ID: " . SMS_SENDER_ID);
            }
            
            // Send the message
            $result = $this->sms->send([
                'to' => $phoneNumber,
                'message' => $message,
                'from' => SMS_SENDER_ID
            ]);
            
            if (DEBUG_MODE) {
                error_log("Reminder SMS Result: " . json_encode($result));
            }
            
            return $result;
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                error_log("Reminder SMS Error: " . $e->getMessage());
                error_log("SMS Error trace: " . $e->getTraceAsString());
            }
            return false;
        }
    }
}