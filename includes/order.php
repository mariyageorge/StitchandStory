<?php
/**
 * Order Class
 * Handles order operations and order items
 */
class Order {
    private $conn;
    private $orders_table = "orders";
    private $order_items_table = "order_items";

    public $order_id;
    public $user_id;
    public $payment_id;
    public $total_amount;
    public $delivery_address;
    public $order_status;

    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create new order with items from cart
     */
    public function createOrder($cart_items) {
        // Start transaction
        $this->conn->begin_transaction();
        
        try {
            // Insert order
            $query = "INSERT INTO " . $this->orders_table . " 
                      (user_id, payment_id, total_amount, delivery_address, order_status) 
                      VALUES (?, ?, ?, ?, 'pending')";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("iids", $this->user_id, $this->payment_id, $this->total_amount, $this->delivery_address);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create order");
            }
            
            $this->order_id = $stmt->insert_id;
            
            // Insert order items
            $item_query = "INSERT INTO " . $this->order_items_table . " 
                          (order_id, product_id, product_name, product_price, quantity, subtotal) 
                          VALUES (?, ?, ?, ?, ?, ?)";
            
            $item_stmt = $this->conn->prepare($item_query);
            
            while ($item = $cart_items->fetch_assoc()) {
                $subtotal = $item['price'] * $item['quantity'];
                $item_stmt->bind_param("iisdid", 
                    $this->order_id, 
                    $item['product_id'], 
                    $item['name'], 
                    $item['price'], 
                    $item['quantity'], 
                    $subtotal
                );
                
                if (!$item_stmt->execute()) {
                    throw new Exception("Failed to add order items");
                }
            }
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback on error
            $this->conn->rollback();
            return false;
        }
    }

    /**
     * Get all orders for a user
     */
    public function getUserOrders($user_id) {
        $query = "SELECT * FROM " . $this->orders_table . " 
                  WHERE user_id = ? 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    /**
     * Get order by ID
     */
    public function getOrderById($order_id) {
        $query = "SELECT o.*, p.transaction_id, p.payment_status, p.payment_date 
                  FROM " . $this->orders_table . " o
                  LEFT JOIN payments p ON o.payment_id = p.payment_id
                  WHERE o.order_id = ? LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    /**
     * Get order items for an order
     */
    public function getOrderItems($order_id) {
        $query = "SELECT oi.*, p.image 
                  FROM " . $this->order_items_table . " oi
                  LEFT JOIN products p ON oi.product_id = p.product_id
                  WHERE oi.order_id = ?
                  ORDER BY oi.order_item_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    /**
     * Update order status
     */
    public function updateOrderStatus($order_id, $status) {
        $query = "UPDATE " . $this->orders_table . " 
                  SET order_status = ? 
                  WHERE order_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $order_id);
        
        return $stmt->execute();
    }

    /**
     * Get order count for user
     */
    public function getOrderCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->orders_table . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] ?? 0;
    }
}
?>

