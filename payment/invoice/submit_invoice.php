<?php
include '../includes/database_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = isset($_POST['status']) ? 1 : 0;
    $invoice_number = $_POST['invoice_number'] ?? '';
    $invoice_date = $_POST['invoice_date'] ?? '';
    $due_date = $_POST['due_date'] ?? '';
    $customer_name = $_POST['customer_name'] ?? '';
    $customer_address = $_POST['customer_address'] ?? '';
    $customer_phone = $_POST['customer_phone'] ?? '';
    $collector_name = $_POST['collector_name'] ?? '';
    $collector_tagline = $_POST['collector_tagline'] ?? '';
    $collector_phone = $_POST['collector_phone'] ?? '';
    $total_amount = $_POST['total_amount'] ?? 0;
    $payment_method = $_POST['payment_method'] ?? '';
    $transaction_date = $_POST['transaction_date'] ?? '';
    $transaction_gateway = $_POST['transaction_gateway'] ?? '';
    $transaction_id = $_POST['transaction_id'] ?? '';
    $amount_paid = $_POST['amount_paid'] ?? 0;

    $sql = "INSERT INTO invoices (status, invoice_number, invoice_date, due_date, customer_name, customer_address, customer_phone, collector_name, collector_tagline, collector_phone, total_amount, payment_method, transaction_date, transaction_gateway, transaction_id, amount_paid) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isssssssssdssssd",
        $status,
        $invoice_number,
        $invoice_date,
        $due_date,
        $customer_name,
        $customer_address,
        $customer_phone,
        $collector_name,
        $collector_tagline,
        $collector_phone,
        $total_amount,
        $payment_method,
        $transaction_date,
        $transaction_gateway,
        $transaction_id,
        $amount_paid
    );
    $stmt->execute();
    $invoiceId = $stmt->insert_id;
    $stmt->close();

    if (isset($_POST['description']) && is_array($_POST['description'])) {
        $sql = "INSERT INTO invoice_items (invoice_id, description, amount) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        foreach ($_POST['description'] as $index => $description) {
            $amount = $_POST['amount'][$index] ?? 0;
            $stmt->bind_param("isd", $invoiceId, $description, $amount);
            $stmt->execute();
        }
        $stmt->close();
    }

    echo "Invoice and items added successfully!";
} else {
    echo "Form not submitted.";
}

$conn->close();
