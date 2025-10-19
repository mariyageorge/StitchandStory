<?php
/**
 * Cart Class
 * Handles shopping cart operations
 */
class Cart {
    private $conn;
    private $table_name = "cart";

    public $cart_id;
    public $user_id;
    public $product_id;
    public $quantity;

    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Add item to cart
     */
    public function addToCart() {
        // Check if item already exists in cart
        $check_query = "SELECT cart_id, quantity FROM " . $this->table_name . " WHERE user_id = ? AND product_id = ?";
        $stmt = $this->conn->prepare($check_query);
        $stmt->bind_param("ii", $this->user_id, $this->product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update quantity
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + $this->quantity;
            
            $update_query = "UPDATE " . $this->table_name . " SET quantity = ? WHERE cart_id = ?";
            $stmt = $this->conn->prepare($update_query);
            $stmt->bind_param("ii", $new_quantity, $row['cart_id']);
            return $stmt->execute();
        } else {
            // Insert new item
            $insert_query = "INSERT INTO " . $this->table_name . " (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($insert_query);
            $stmt->bind_param("iii", $this->user_id, $this->product_id, $this->quantity);
            return $stmt->execute();
        }
    }

    /**
     * Get cart items for user
     */
    public function getCartItems($user_id) {
        $query = "SELECT c.cart_id, c.quantity, p.product_id, p.name, p.price, p.image 
                  FROM " . $this->table_name . " c
                  INNER JOIN products p ON c.product_id = p.product_id
                  WHERE c.user_id = ?
                  ORDER BY c.added_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        return $stmt->get_result();
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity($cart_id, $quantity) {
        $query = "UPDATE " . $this->table_name . " SET quantity = ? WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $quantity, $cart_id);
        return $stmt->execute();
    }

    /**
     * Remove item from cart
     */
    public function removeItem($cart_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE cart_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $cart_id);
        return $stmt->execute();
    }

    /**
     * Clear cart for user
     */
    public function clearCart($user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }

    /**
     * Get cart total
     */
    public function getCartTotal($user_id) {
        $query = "SELECT SUM(c.quantity * p.price) as total
                  FROM " . $this->table_name . " c
                  INNER JOIN products p ON c.product_id = p.product_id
                  WHERE c.user_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['total'] ?? 0;
    }

    /**
     * Get cart item count
     */
    public function getCartCount($user_id) {
        $query = "SELECT SUM(quantity) as count FROM " . $this->table_name . " WHERE user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        return $row['count'] ?? 0;
    }
}
?>

