<?php
include '../includes/database_connection.php';

if (!isset($_GET['invoice_id'])) {
    die("Invoice ID not provided.");
}

$invoice_id = intval($_GET['invoice_id']);

$sql_delete_items = "DELETE FROM invoice_items WHERE invoice_id = ?";
$stmt_items = $conn->prepare($sql_delete_items);
$stmt_items->bind_param("i", $invoice_id);
$stmt_items->execute();

$sql_delete_invoice = "DELETE FROM invoices WHERE invoice_id = ?";
$stmt_invoice = $conn->prepare($sql_delete_invoice);
$stmt_invoice->bind_param("i", $invoice_id);
$stmt_invoice->execute();

header("Location: index.php");
exit();
