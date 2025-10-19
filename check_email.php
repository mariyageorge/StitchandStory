<?php
/**
 * AJAX endpoint to check if email already exists
 */
require_once 'includes/db.php';

header('Content-Type: application/json');

if (isset($_POST['email'])) {
    $email = trim($_POST['email']);
    
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo json_encode(['exists' => $result->num_rows > 0]);
} else {
    echo json_encode(['error' => 'Email parameter missing']);
}
?>

