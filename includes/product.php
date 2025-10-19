<?php
/**
 * Product Class
 * Handles product operations
 */
class Product {
    private $conn;
    private $table_name = "products";

    public $product_id;
    public $name;
    public $category;
    public $description;
    public $price;
    public $image;

    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all products
     */
    public function getAllProducts($search = '', $sort = 'name', $order = 'ASC') {
        $query = "SELECT * FROM " . $this->table_name;
        
        // Add search condition
        if (!empty($search)) {
            $query .= " WHERE name LIKE ? OR category LIKE ? OR description LIKE ?";
        }
        
        // Add sorting
        $allowed_sort = ['name', 'price', 'category', 'created_at'];
        $allowed_order = ['ASC', 'DESC'];
        
        if (in_array($sort, $allowed_sort) && in_array($order, $allowed_order)) {
            $query .= " ORDER BY $sort $order";
        }

        $stmt = $this->conn->prepare($query);
        
        if (!empty($search)) {
            $search_param = "%$search%";
            $stmt->bind_param("sss", $search_param, $search_param, $search_param);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        return $result;
    }

    /**
     * Get featured products (first 4)
     */
    public function getFeaturedProducts($limit = 4) {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Get product by ID
     */
    public function getProductById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE product_id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        
        return null;
    }

    /**
     * Get all categories
     */
    public function getAllCategories() {
        $query = "SELECT DISTINCT category FROM " . $this->table_name . " ORDER BY category";
        $result = $this->conn->query($query);
        
        $categories = [];
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row['category'];
        }
        
        return $categories;
    }
}
?>

