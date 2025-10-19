<?php
/**
 * Payment Class
 * Handles payment operations
 */
class Payment {
    private $conn;
    private $table_name = "payments";

    public $payment_id;
    public $user_id;
    public $amount;
    public $transaction_id;
    public $payment_status;

    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Save payment details
     */
    public function savePayment() {
        $query = "INSERT INTO " . $this->table_name . " (user_id, amount, transaction_id, payment_status) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("idss", $this->user_id, $this->amount, $this->transaction_id, $this->payment_status);
        
        if ($stmt->execute()) {
            $this->payment_id = $stmt->insert_id;
            return true;
        }
        
        return false;
    }

    /**
     * Get user's payment history
     */
    public function getPaymentHistory($user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = ? ORDER BY payment_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    /**
     * Get payment by transaction ID
     */
    public function getPaymentByTransactionId($transaction_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE transaction_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $transaction_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    /**
     * Update payment status
     */
    public function updatePaymentStatus($transaction_id, $status) {
        $query = "UPDATE " . $this->table_name . " SET payment_status = ? WHERE transaction_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $status, $transaction_id);
        
        return $stmt->execute();
    }
}
?>

