<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'includes/db.php';
require_once 'includes/order.php';
require_once 'includes/fpdf/fpdf.php';

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id == 0) {
    die('Invalid order ID');
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize Order object
$order_obj = new Order($db);

// Get order details
$order = $order_obj->getOrderById($order_id);

// Check if order exists and belongs to user
if (!$order || $order['user_id'] != $_SESSION['user_id']) {
    die('Order not found or unauthorized access');
}

// Get order items
$order_items = $order_obj->getOrderItems($order_id);

// Create PDF
class ReceiptPDF extends FPDF {
    function Header() {
        // Logo/Title
        $this->SetFillColor(255, 107, 157); // Pink color
        $this->Rect(0, 0, 210, 40, 'F');
        
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 24);
        $this->Cell(0, 20, '', 0, 1);
        $this->Cell(0, 10, 'Stitch & Story', 0, 1, 'C');
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 5, 'Handmade Crochet Products', 0, 1, 'C');
        $this->Ln(10);
        
        $this->SetTextColor(0, 0, 0);
    }
    
    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Thank you for shopping with Stitch & Story! | Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new ReceiptPDF();
$pdf->AddPage();

// Receipt Title
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetTextColor(255, 107, 157);
$pdf->Cell(0, 10, 'ORDER RECEIPT', 0, 1, 'C');
$pdf->Ln(5);

// Order Information
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(40, 8, 'Order ID:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, '#' . $order['order_id'], 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Order Date:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, date('F j, Y, g:i a', strtotime($order['created_at'])), 0, 1);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Order Status:', 0, 0);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, ucfirst($order['order_status']), 0, 1);

if (isset($order['transaction_id'])) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(40, 8, 'Transaction ID:', 0, 0);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 8, $order['transaction_id'], 0, 1);
}

$pdf->Ln(5);

// Customer Information
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(255, 235, 245);
$pdf->Cell(0, 10, 'Customer Information', 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, 'Name: ' . $_SESSION['username'] . "\nEmail: " . $_SESSION['email']);
$pdf->Ln(3);

// Delivery Address
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Delivery Address', 0, 1, 'L', true);
$pdf->SetFont('Arial', '', 11);
$pdf->MultiCell(0, 6, $order['delivery_address']);
$pdf->Ln(3);

// Order Items Table
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Order Items', 0, 1, 'L', true);
$pdf->Ln(2);

// Table Header
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(255, 192, 203); // Light pink
$pdf->Cell(80, 8, 'Product', 1, 0, 'L', true);
$pdf->Cell(30, 8, 'Price', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Quantity', 1, 0, 'C', true);
$pdf->Cell(40, 8, 'Subtotal', 1, 1, 'C', true);

// Table Body
$pdf->SetFont('Arial', '', 10);
$pdf->SetFillColor(255, 245, 250); // Very light pink

$fill = false;
while ($item = $order_items->fetch_assoc()) {
    $pdf->Cell(80, 8, substr($item['product_name'], 0, 35), 1, 0, 'L', $fill);
    $pdf->Cell(30, 8, 'Rs.' . number_format($item['product_price'], 2), 1, 0, 'C', $fill);
    $pdf->Cell(30, 8, $item['quantity'], 1, 0, 'C', $fill);
    $pdf->Cell(40, 8, 'Rs.' . number_format($item['subtotal'], 2), 1, 1, 'C', $fill);
    $fill = !$fill;
}

// Total
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(255, 192, 203);
$pdf->Cell(140, 10, 'TOTAL AMOUNT', 1, 0, 'R', true);
$pdf->Cell(40, 10, 'Rs.' . number_format($order['total_amount'], 2), 1, 1, 'C', true);

$pdf->Ln(10);

// Payment Status
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetTextColor(0, 128, 0); // Green color
if (isset($order['payment_status']) && $order['payment_status'] == 'success') {
    $pdf->Cell(0, 10, 'Payment Status: PAID', 0, 1, 'C');
}
$pdf->SetTextColor(0, 0, 0);

$pdf->Ln(5);

// Footer Message
$pdf->SetFont('Arial', 'I', 10);
$pdf->SetTextColor(100, 100, 100);
$pdf->MultiCell(0, 5, "Thank you for your purchase! Your order will be processed and shipped soon.\n\nFor any queries, please contact us at support@stitchandstory.com");

// Output PDF
$pdf->Output('D', 'Receipt_Order_' . $order_id . '.pdf');
?>

