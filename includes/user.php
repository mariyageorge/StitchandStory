<?php
/**
 * User Class
 * Handles user registration, login, and authentication
 */
class User {
    private $conn;
    private $table_name = "users";

    public $user_id;
    public $username;
    public $email;
    public $password;

    /**
     * Constructor
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Register new user
     */
    public function register() {
        // Check if email already exists
        if ($this->emailExists()) {
            return ['success' => false, 'message' => 'Email already exists!'];
        }

        // Check if username already exists
        if ($this->usernameExists()) {
            return ['success' => false, 'message' => 'Username already taken!'];
        }

        // Hash password
        $hashed_password = password_hash($this->password, PASSWORD_DEFAULT);

        // Insert user
        $query = "INSERT INTO " . $this->table_name . " (username, email, password) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $this->username, $this->email, $hashed_password);

        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Registration successful!'];
        }

        return ['success' => false, 'message' => 'Registration failed!'];
    }

    /**
     * Login user
     */
    public function login() {
        $query = "SELECT user_id, username, email, password FROM " . $this->table_name . " WHERE email = ? OR username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $this->email, $this->email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            
            if (password_verify($this->password, $row['password'])) {
                $this->user_id = $row['user_id'];
                $this->username = $row['username'];
                $this->email = $row['email'];
                
                // Set session
                $_SESSION['user_id'] = $this->user_id;
                $_SESSION['username'] = $this->username;
                $_SESSION['email'] = $this->email;
                
                return ['success' => true, 'message' => 'Login successful!'];
            }
        }

        return ['success' => false, 'message' => 'Invalid credentials!'];
    }

    /**
     * Check if email exists
     */
    private function emailExists() {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE email = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Check if username exists
     */
    private function usernameExists() {
        $query = "SELECT user_id FROM " . $this->table_name . " WHERE username = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $this->username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Logout user
     */
    public static function logout() {
        session_destroy();
        header("Location: login.php");
        exit();
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
}
?>

