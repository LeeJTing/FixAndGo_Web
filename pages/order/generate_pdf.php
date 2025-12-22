<?php
require_once "../../_base.php";
require_once "../../library/fpdf.php";
require '../../controller/order-controller.php';

// Require login
$user_id = temp('USER_ID');
if (!$user_id) {
    header("Location: {$rootDir}/pages/auth/login.php");
    exit;
}

// Get order ID
$order_id = (int)($_GET['id'] ?? 0);

$order = getOrderById($order_id);
$items = getOrderItem($order_id);
$user = getUserOrder($order_id, $order->user_id);
$payment = getPayment($order_id);

// Ownership check (basic): only allow owner to download.
if ($order && (string)$order->user_id !== (string)$user_id) {
    http_response_code(403);
    die('Order not found or not yours.');
}

if (!$order || empty($items)) {
    die("Invalid order.");
}

$pdf = new FPDF();
$pdf->AddPage();

// ================= HEADER =================
$pdf->SetFont('Helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Fix & Go - Invoice', 0, 1, 'C');
$pdf->Ln(5);

// ================= ORDER INFO =================
$pdf->SetFont('Helvetica', '', 12);
$pdf->Cell(0, 8, "Order ID: {$order->order_id}", 0, 1);
$pdf->Cell(0, 8, "Order Date: " . date('d M Y, H:i', strtotime($order->order_at)), 0, 1);
$pdf->Cell(0, 8, "Order Status: {$order->status}", 0, 1);
$pdf->Ln(5);

// ================= SHIPPING =================
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Shipping Information', 0, 1);

$pdf->SetFont('Helvetica', '', 11);
$pdf->MultiCell(
    0,
    6,
    "{$user->address_one}\n" .
        ($user->address_two ? $user->address_two . "\n" : '') .
        ($user->address_three ? $user->address_three . "\n" : '') .
        "{$user->post_code} {$user->state}\n{$user->country}"
);
$pdf->Ln(4);

// ================= PAYMENT =================
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Payment Information', 0, 1);

$pdf->SetFont('Helvetica', '', 11);
$rawPaymentMethod = $payment->payment_method ?? '';
$paymentMethod = $rawPaymentMethod;

// Match the labels used in the HTML receipt page.
if ($rawPaymentMethod === 'Bank Transfer') {
    $paymentMethod = 'Online Banking';
} elseif ($rawPaymentMethod === 'Credit Card' || $rawPaymentMethod === 'Debit Card') {
    $paymentMethod = 'Credit/Debit Card';
} elseif ($rawPaymentMethod === 'Cash') {
    $paymentMethod = 'Cash on Delivery';
} elseif ($rawPaymentMethod === 'Loyalty Points') {
    $paymentMethod = 'Loyalty Points';
} elseif ($rawPaymentMethod === '' || $rawPaymentMethod === null) {
    $paymentMethod = 'Unknown';
}

// If loyalty points fully covered the total, show Loyalty Points even if something stored incorrectly.
// Also cover older orders where utilize_point flag might be missing.
if (((int)round(((float)$order->total_price) * 100) === 0)) {
    $shipping_fee_guard = 10.00;
    $pre_total_cents_guard = (int)round((($items_total + $shipping_fee_guard) * 100));
    $final_total_cents_guard = (int)round(((float)$order->total_price) * 100);
    $discount_cents_guard = max(0, $pre_total_cents_guard - $final_total_cents_guard);
    $used_points_guard = (int)round($discount_cents_guard / 10);

    if ($used_points_guard > 0 || (int)($order->utilize_point ?? 0) === 1) {
        $paymentMethod = 'Loyalty Points';
    }
}

$paymentId = $payment->payment_id ?? '-';
$pdf->Cell(0, 6, "Payment Method: {$paymentMethod}", 0, 1);
$pdf->Cell(0, 6, "Payment Status: {$order->payment_status}", 0, 1);
$pdf->Cell(0, 6, "Transaction ID: {$paymentId}", 0, 1);
$pdf->Ln(5);

// ================= ITEMS TABLE =================
$pdf->SetFont('Helvetica', 'B', 11);
$pdf->Cell(80, 8, 'Product', 1);
$pdf->Cell(30, 8, 'Unit Price', 1);
$pdf->Cell(20, 8, 'Qty', 1);
$pdf->Cell(30, 8, 'Subtotal', 1);
$pdf->Ln();

$pdf->SetFont('Helvetica', '', 11);

$total_qty = 0;
$items_total = 0.0;

foreach ($items as $item) {
    $pdf->Cell(80, 8, $item->product_name, 1);
    $pdf->Cell(30, 8, 'RM ' . number_format($item->unit_price, 2), 1);
    $pdf->Cell(20, 8, $item->qty, 1);
    $pdf->Cell(30, 8, 'RM ' . number_format($item->subtotal, 2), 1);
    $pdf->Ln();

    $total_qty += $item->qty;
    $items_total += (float)$item->subtotal;
}

// ================= SUMMARY =================
$pdf->Ln(5);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Order Summary', 0, 1);

$pdf->SetFont('Helvetica', '', 11);
$pdf->Cell(0, 6, "Total Items: " . count($items), 0, 1);
$pdf->Cell(0, 6, "Total Quantity: {$total_qty}", 0, 1);

// Totals breakdown (shipping fixed at RM 10.00)
$shipping_fee = 10.00;
$pre_total_cents = (int)round((($items_total + $shipping_fee) * 100));
$final_total_cents = (int)round(((float)$order->total_price) * 100);
$discount_cents = max(0, $pre_total_cents - $final_total_cents);

// Loyalty rule: 10 points = RM 1.00 (1 point = RM 0.10 => 10 cents)
$point_value_cents = 10;
$used_points = (int)round($discount_cents / $point_value_cents);
$discount_rm = $discount_cents / 100;

$pdf->Ln(2);
$pdf->Cell(0, 6, "Subtotal: RM " . number_format($items_total, 2), 0, 1);
$pdf->Cell(0, 6, "Shipping Fee: RM " . number_format($shipping_fee, 2), 0, 1);
if ($used_points > 0 && $discount_cents > 0) {
    $pdf->Cell(0, 6, "Loyalty Points Deducted: -RM " . number_format($discount_rm, 2) . " ({$used_points} points)", 0, 1);
}

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 10, "Total Amount: RM " . number_format((float)$order->total_price, 2), 0, 1);

// ================= OUTPUT =================
$pdf->Output("I", "invoice_order_{$order_id}.pdf");
