<?php

require_once __DIR__ . '/../DAO/order-dao.php';
require_once __DIR__ . '/../email/email.php';
require_once __DIR__ . '/../library/fpdf.php';

function _fixandgo_compute_used_points(float $itemsTotal, float $shippingFee, float $finalTotal): array
{
    $gross = (float)$itemsTotal + (float)$shippingFee;

    $grossCents = (int)round($gross * 100);
    $finalCents = (int)round(((float)$finalTotal) * 100);
    $discountCents = $grossCents - $finalCents;
    if ($discountCents < 0) $discountCents = 0;

    // 1 point = RM 0.10 => 10 cents
    $usedPoints = ($discountCents > 0) ? (int)round($discountCents / 10) : 0;
    if ($usedPoints < 0) $usedPoints = 0;

    $discountRm = $discountCents / 100;
    $usedPointsRm = $usedPoints * 0.10;

    return [
        'used_points' => $usedPoints,
        'used_points_rm' => $usedPointsRm,
        'discount_rm' => $discountRm,
        'discount_cents' => $discountCents,
        'gross_rm' => $gross
    ];
}

function sendReceiptEmailForOrder(int $order_id, array $overrides = []): bool
{
    global $_db;

    $order = getOrderWithSelectedAddress($order_id);
    if (!$order || empty($order->email)) return false;

    $items = getAllProductByOrderId($order_id);
    if (!$items || !is_array($items)) $items = [];

    $paymentRow = getPaymentRowByOrderIdDao($order_id);
    $paymentMethod = $paymentRow->payment_method ?? '';

    $shippingFee = 10.00;
    $itemsTotal = 0.0;
    foreach ($items as $it) {
        $itemsTotal += ((float)$it->unit_price * (int)$it->qty);
    }

    $computed = _fixandgo_compute_used_points($itemsTotal, $shippingFee, (float)$order->total_price);
    $used_points = (int)($computed['used_points'] ?? 0);
    $used_points_rm = (float)($computed['used_points_rm'] ?? 0.0);
    $discount_rm = (float)($computed['discount_rm'] ?? 0.0);

    $statusLabel = (string)($overrides['status'] ?? ($order->status ?? ''));
    $paymentLabel = (string)($overrides['payment_method'] ?? ($paymentMethod ?: ''));
    if ($statusLabel === '') $statusLabel = 'Processing';
    if ($paymentLabel === '') $paymentLabel = 'Unknown';

    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->SetFont('Helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Fix & GO - Receipt / Invoice', 0, 1, 'C');
    $pdf->Ln(4);

    $pdf->SetFont('Helvetica', '', 11);
    $pdf->Cell(0, 7, 'Order ID: ' . (int)$order->order_id, 0, 1);
    $dateStr = '';
    if (!empty($order->order_at)) {
        try {
            $dateStr = date('d M Y, H:i', strtotime((string)$order->order_at));
        } catch (Throwable $e) {
            $dateStr = '';
        }
    }
    $pdf->Cell(0, 7, 'Date: ' . ($dateStr ?: date('d M Y, H:i')), 0, 1);
    $pdf->Cell(0, 7, 'Payment Method: ' . $paymentLabel, 0, 1);
    $pdf->Cell(0, 7, 'Status: ' . $statusLabel, 0, 1);
    $pdf->Ln(3);

    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Delivery Address', 0, 1);
    $pdf->SetFont('Helvetica', '', 11);

    $addressLines = '';
    if (!empty($order->address_name)) $addressLines .= $order->address_name . "\n";
    if (!empty($order->address_one)) $addressLines .= $order->address_one . "\n";
    if (!empty($order->address_two)) $addressLines .= $order->address_two . "\n";
    if (!empty($order->address_three)) $addressLines .= $order->address_three . "\n";
    $addressLines .= trim((string)$order->post_code . ' ' . (string)$order->state) . "\n";
    $addressLines .= (string)$order->country;

    $pdf->MultiCell(0, 6, $addressLines);
    $pdf->Ln(3);

    $pdf->SetFont('Helvetica', 'B', 11);
    $pdf->Cell(90, 8, 'Item', 1);
    $pdf->Cell(20, 8, 'Qty', 1);
    $pdf->Cell(35, 8, 'Unit (RM)', 1);
    $pdf->Cell(35, 8, 'Amount (RM)', 1);
    $pdf->Ln();

    $pdf->SetFont('Helvetica', '', 11);
    foreach ($items as $it) {
        $qty = (int)$it->qty;
        $unit = (float)$it->unit_price;
        $line = $qty * $unit;

        $name = (string)($it->product_name ?? 'Item');
        if (function_exists('mb_strlen') && mb_strlen($name) > 40) {
            $name = mb_substr($name, 0, 37) . '...';
        } elseif (strlen($name) > 40) {
            $name = substr($name, 0, 37) . '...';
        }

        $pdf->Cell(90, 8, $name, 1);
        $pdf->Cell(20, 8, (string)$qty, 1, 0, 'R');
        $pdf->Cell(35, 8, number_format($unit, 2), 1, 0, 'R');
        $pdf->Cell(35, 8, number_format($line, 2), 1, 0, 'R');
        $pdf->Ln();
    }

    $pdf->Ln(4);
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->Cell(0, 7, 'Subtotal: RM ' . number_format($itemsTotal, 2), 0, 1);
    $pdf->Cell(0, 7, 'Shipping Fee: RM ' . number_format($shippingFee, 2), 0, 1);
    if ($used_points > 0 && $discount_rm > 0) {
        $pdf->Cell(
            0,
            7,
            'Loyalty Points Deducted: -RM ' . number_format($discount_rm, 2) . ' (' . $used_points . ' points)',
            0,
            1
        );
    }
    $pdf->SetFont('Helvetica', 'B', 12);
    $pdf->Cell(0, 9, 'Total: RM ' . number_format((float)$order->total_price, 2), 0, 1);

    $pdfBytes = $pdf->Output('S');

    try {
        $mail = get_mail();
        $mail->addAddress((string)$order->email, (string)($order->user_name ?? 'Customer'));
        $mail->isHTML(true);
        $mail->Subject = 'Fix & GO Receipt - Order #' . (int)$order->order_id;

        $safeName = htmlspecialchars((string)($order->user_name ?? 'Customer'));
        $safeOrderId = (int)$order->order_id;
        $safeTotal = number_format((float)$order->total_price, 2);
        $safeStatus = htmlspecialchars($statusLabel);
        $safePay = htmlspecialchars($paymentLabel);

        $pointsHtml = '';
        $pointsText = '';
        if ($used_points > 0 && $discount_rm > 0) {
            $pointsHtml = '<div>Loyalty Points Deducted: <b>-RM ' . number_format($discount_rm, 2) . '</b> (' . (int)$used_points . ' points)</div>';
            $pointsText = 'Loyalty Points Deducted: -RM ' . number_format($discount_rm, 2) . ' (' . (int)$used_points . " points)\n";
        }

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;line-height:1.5;color:#111827'>
          <h2 style='margin:0 0 8px'>Receipt / Invoice</h2>
          <div style='margin:0 0 14px'>Hi <b>{$safeName}</b>, your order payment has been confirmed.</div>
          <div style='padding:12px;border:1px solid #e5e7eb;border-radius:8px'>
            <div><b>Order #{$safeOrderId}</b></div>
            <div>Total: <b>RM {$safeTotal}</b></div>
            {$pointsHtml}
            <div>Status: {$safeStatus}</div>
            <div>Payment Method: {$safePay}</div>
          </div>
          <p style='margin:14px 0 0'>Your invoice is attached as a PDF.</p>
        </div>";

        $mail->AltBody = "Hi " . ($order->user_name ?? 'Customer') . "\n\n" .
            "Order #{$order_id}\n" .
            "Total: RM {$safeTotal}\n" .
            $pointsText .
            "Status: {$statusLabel}\n" .
            "Payment Method: {$paymentLabel}\n\n" .
            "Invoice is attached as PDF.";

        $mail->addStringAttachment($pdfBytes, 'invoice_order_' . (int)$order->order_id . '.pdf', 'base64', 'application/pdf');
        $mail->send();
        return true;
    } catch (Throwable $e) {
        error_log('Receipt email failed for order ' . $order_id . ': ' . $e->getMessage());
        return false;
    }
}
