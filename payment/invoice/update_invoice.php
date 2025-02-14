<?php
include '../includes/database_connection.php';

$invoice_id = intval($_POST['invoice_id']);
$invoice_number = $_POST['invoice_number'];
$invoice_date = $_POST['invoice_date'];
$due_date = $_POST['due_date'];
$customer_name = $_POST['customer_name'];
$customer_address = $_POST['customer_address'];
$customer_phone = $_POST['customer_phone'];
$collector_name = $_POST['collector_name'];
$collector_tagline = $_POST['collector_tagline'];
$collector_phone = $_POST['collector_phone'];
$payment_method = $_POST['payment_method'];
$transaction_date = $_POST['transaction_date'];
$transaction_gateway = $_POST['transaction_gateway'];
$transaction_id = $_POST['transaction_id'];
$amount_paid = floatval($_POST['amount_paid']);
$status = isset($_POST['status']) ? 1 : 0;
$total_amount = floatval($_POST['total_amount']);

$sql = "UPDATE invoices SET 
        invoice_number = ?, 
        invoice_date = ?, 
        due_date = ?, 
        customer_name = ?, 
        customer_address = ?, 
        customer_phone = ?, 
        collector_name = ?, 
        collector_tagline = ?, 
        collector_phone = ?, 
        payment_method = ?, 
        transaction_date = ?, 
        transaction_gateway = ?, 
        transaction_id = ?, 
        amount_paid = ?, 
        status = ?, 
        total_amount = ? 
        WHERE invoice_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "sssssssssssssdidi",
    $invoice_number,
    $invoice_date,
    $due_date,
    $customer_name,
    $customer_address,
    $customer_phone,
    $collector_name,
    $collector_tagline,
    $collector_phone,
    $payment_method,
    $transaction_date,
    $transaction_gateway,
    $transaction_id,
    $amount_paid,
    $status,
    $total_amount,
    $invoice_id
);
$stmt->execute();

if (isset($_POST['item_id']) && is_array($_POST['item_id'])) {
    foreach ($_POST['item_id'] as $index => $item_id) {
        $description = $_POST['description'][$index];
        $amount = floatval($_POST['amount'][$index]);

        if ($item_id === 'new') {
            $sql = "INSERT INTO invoice_items (invoice_id, description, amount) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isd", $invoice_id, $description, $amount);
            $stmt->execute();
        } else {
            $sql = "UPDATE invoice_items SET description = ?, amount = ? WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdi", $description, $amount, $item_id);
            $stmt->execute();
        }
    }
}

echo "Invoice and items updated successfully!";
