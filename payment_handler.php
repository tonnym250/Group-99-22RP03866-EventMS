<?php
// payment_handler.php - Handles payment operations

require_once __DIR__ . '/vendor/autoload.php';
use AfricasTalking\SDK\AfricasTalking;

class PaymentHandler {
    private $AT;
    private $payments;
    private $conn;

    public function __construct() {
        global $conn;
        $this->conn = $conn;
        
        // Initialize Africa's Talking SDK
        $this->AT = new AfricasTalking(AT_USERNAME, AT_API_KEY);
        
        // Since there's no direct payment service in the SDK, we'll simulate it
        // In a real implementation, you would use the appropriate service
        $this->payments = null; // Remove the non-existent method call
    }

    public function processPayment($phoneNumber, $amount, $description, $registrationId) {
        try {
            // Format phone number to international format if needed
            if (substr($phoneNumber, 0, 1) !== '+') {
                // Add country code (e.g., for Rwanda: +250)
                $phoneNumber = '+250' . substr($phoneNumber, 1);
            }
            
            // Generate a unique transaction reference
            $transactionRef = 'EVT' . time() . rand(100, 999);
            
            // In a real implementation, you would call the Africa's Talking payment API
            // For now, we'll simulate a successful payment response
            $result = [
                'status' => 'Success',
                'transactionId' => $transactionRef,
                'description' => 'Payment simulation for ' . $description
            ];
            
            // Log the payment request result if in debug mode
            if (DEBUG_MODE) {
                error_log("Payment Request: " . json_encode($result));
            }
            
            // Update the registration with the payment reference and set status to completed
            $stmt = $this->conn->prepare("
                UPDATE registrations 
                SET payment_reference = :payment_reference,
                    payment_status = 'completed'
                WHERE id = :registration_id
            ");
            
            $stmt->execute([
                'payment_reference' => $transactionRef,
                'registration_id' => $registrationId
            ]);
            
            return $result;
            
        } catch (Exception $e) {
            // Log the error if in debug mode
            if (DEBUG_MODE) {
                error_log("Payment Error: " . $e->getMessage());
            }
            
            throw $e;
        }
    }

    // This method would be called by your payment callback URL
    public function handlePaymentCallback($data) {
        try {
            // Extract data from the callback
            $status = $data['status'] ?? '';
            $transactionId = $data['transactionId'] ?? '';
            $providerRefId = $data['providerRefId'] ?? '';
            
            // Find the registration by the transaction reference
            $stmt = $this->conn->prepare("
                SELECT * FROM registrations 
                WHERE payment_reference = :payment_reference
            ");
            
            $stmt->execute(['payment_reference' => $transactionId]);
            $registration = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$registration) {
                // Log that we received a payment for an unknown registration
                if (DEBUG_MODE) {
                    error_log("Payment callback for unknown registration: " . $transactionId);
                }
                return false;
            }
            
            // Update the payment status
            $stmt = $this->conn->prepare("
                UPDATE registrations 
                SET payment_status = :payment_status 
                WHERE id = :registration_id
            ");
            
            $stmt->execute([
                'payment_status' => ($status === 'Success') ? 'completed' : 'failed',
                'registration_id' => $registration['id']
            ]);
            
            // If payment was successful, send confirmation SMS
            if ($status === 'Success') {
                // Get the event details
                $stmt = $this->conn->prepare("
                    SELECT * FROM events 
                    WHERE id = :event_id
                ");
                
                $stmt->execute(['event_id' => $registration['event_id']]);
                $event = $stmt->fetch(PDO::FETCH_ASSOC);
                
                // Send confirmation SMS
                $smsHandler = new SmsHandler();
                $smsHandler->sendPaymentConfirmationSms(
                    $registration['phone_number'],
                    $registration['user_name'],
                    $event['name'],
                    $event['price']
                );
            }
            
            return true;
            
        } catch (Exception $e) {
            // Log the error if in debug mode
            if (DEBUG_MODE) {
                error_log("Payment Callback Error: " . $e->getMessage());
            }
            
            return false;
        }
    }
}