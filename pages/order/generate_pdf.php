<?php
require_once "../../_base.php";
require_once "../../library/fpdf.php";
require '../../controller/order-controller.php';

// Get order ID
$order_id = (int)($_GET['id'] ?? 0);

$order = getOrderById($order_id);
$items = getOrderItem($order_id);
$user = getUserOrder($order_id, $order->user_id);
$payment = getPayment($order_id);

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
$pdf->MultiCell(0, 6,
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
$pdf->Cell(0, 6, "Payment Method: {$payment->payment_id}", 0, 1);
$pdf->Cell(0, 6, "Payment Status: {$order->payment_status}", 0, 1);
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

foreach ($items as $item) {
    $pdf->Cell(80, 8, $item->product_name, 1);
    $pdf->Cell(30, 8, 'RM ' . number_format($item->unit_price, 2), 1);
    $pdf->Cell(20, 8, $item->qty, 1);
    $pdf->Cell(30, 8, 'RM ' . number_format($item->subtotal, 2), 1);
    $pdf->Ln();

    $total_qty += $item->qty;
}

// ================= SUMMARY =================
$pdf->Ln(5);
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 8, 'Order Summary', 0, 1);

$pdf->SetFont('Helvetica', '', 11);
$pdf->Cell(0, 6, "Total Items: " . count($items), 0, 1);
$pdf->Cell(0, 6, "Total Quantity: {$total_qty}", 0, 1);

$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(0, 10, "Total Amount: RM " . number_format($order->total_price, 2), 0, 1);

// ================= OUTPUT =================
$pdf->Output("I", "invoice_order_{$order_id}.pdf");
