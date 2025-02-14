<?php
include '../includes/database_connection.php';

if (!isset($_GET['invoice_id']) || empty($_GET['invoice_id'])) {
    die("ID Invoice tidak ditemukan.");
}

$invoice_id = intval($_GET['invoice_id']); // Ambil invoice_id dari URL dan pastikan sebagai integer

$sql = "SELECT * FROM invoices WHERE invoice_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("ID Invoice tidak ditemukan.");
}

$invoice = $result->fetch_assoc(); // Ambil data invoice

$sql_items = "SELECT * FROM invoice_items WHERE invoice_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $invoice_id);
$stmt_items->execute();
$items_result = $stmt_items->get_result();
$items = $items_result->fetch_all(MYSQLI_ASSOC); // Ambil semua data items
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Invoice</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .item {
            margin-bottom: 20px;
        }

        .remove-item {
            margin-left: 10px;
        }

        #add-item {
            margin-bottom: 20px;
        }

        .invoice-items-section {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Invoice</h2>
        <form action="update_invoice.php" method="post">
            <input type="hidden" name="invoice_id" value="<?= $invoice['invoice_id'] ?>">

            <div class="form-group">
                <label>Invoice Number</label>
                <input type="text" class="form-control" name="invoice_number" value="<?= htmlspecialchars($invoice['invoice_number']) ?>" required>
            </div>
            <div class="form-group">
                <label>Invoice Date</label>
                <input type="date" class="form-control" name="invoice_date" value="<?= $invoice['invoice_date'] ?>" required>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" class="form-control" name="due_date" value="<?= $invoice['due_date'] ?>" required>
            </div>
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" class="form-control" name="customer_name" value="<?= htmlspecialchars($invoice['customer_name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Customer Address</label>
                <textarea class="form-control" name="customer_address"><?= htmlspecialchars($invoice['customer_address']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Customer Phone</label>
                <input type="text" class="form-control" name="customer_phone" value="<?= htmlspecialchars($invoice['customer_phone']) ?>">
            </div>
            <div class="form-group">
                <label>Collector Name</label>
                <input type="text" class="form-control" name="collector_name" value="<?= htmlspecialchars($invoice['collector_name']) ?>">
            </div>
            <div class="form-group">
                <label>Collector Tagline</label>
                <input type="text" class="form-control" name="collector_tagline" value="<?= htmlspecialchars($invoice['collector_tagline']) ?>">
            </div>
            <div class="form-group">
                <label>Collector Phone</label>
                <input type="text" class="form-control" name="collector_phone" value="<?= htmlspecialchars($invoice['collector_phone']) ?>">
            </div>
            <div class="form-group">
                <label>Payment Method</label>
                <input type="text" class="form-control" name="payment_method" value="<?= htmlspecialchars($invoice['payment_method']) ?>">
            </div>
            <div class="form-group">
                <label>Transaction Date</label>
                <input type="datetime-local" class="form-control" name="transaction_date" value="<?= str_replace(' ', 'T', $invoice['transaction_date']) ?>">
            </div>
            <div class="form-group">
                <label>Transaction Gateway</label>
                <input type="text" class="form-control" name="transaction_gateway" value="<?= htmlspecialchars($invoice['transaction_gateway']) ?>">
            </div>
            <div class="form-group">
                <label>Transaction ID</label>
                <input type="text" class="form-control" name="transaction_id" value="<?= htmlspecialchars($invoice['transaction_id']) ?>">
            </div>
            <div class="form-group">
                <label>Amount Paid</label>
                <input type="number" step="0.01" class="form-control" name="amount_paid" value="<?= $invoice['amount_paid'] ?>">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="status" id="status" value="1" <?= $invoice['status'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="status">Paid</label>
            </div>

            <div class="invoice-items-section">
                <h4>Invoice Items</h4>
                <div id="items">
                    <?php foreach ($items as $item) : ?>
                        <div class="item">
                            <input type="hidden" name="item_id[]" value="<?= $item['item_id'] ?>">
                            <div class="form-group">
                                <label>Description</label>
                                <input type="text" class="form-control" name="description[]" value="<?= htmlspecialchars($item['description']) ?>">
                            </div>
                            <div class="form-group d-flex align-items-end">
                                <div class="flex-grow-1">
                                    <label>Amount</label>
                                    <input type="number" step="0.01" class="form-control amount" name="amount[]" value="<?= $item['amount'] ?>">
                                </div>
                                <button type="button" class="btn btn-danger remove-item">Remove Item</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-item" class="btn btn-primary">Add More Item</button>
            </div>

            <div class="form-group">
                <label>Total Amount</label>
                <input type="number" step="0.01" class="form-control" name="total_amount" id="total_amount" value="<?= $invoice['total_amount'] ?>" readonly>
            </div>

            <button type="submit" class="btn btn-success">Update Invoice</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add item
            $('#add-item').click(function() {
                var newItem = `<div class="item">
            <input type="hidden" name="item_id[]" value="new">
            <div class="form-group">
                <label>Description</label>
                <input type="text" class="form-control" name="description[]">
            </div>
            <div class="form-group d-flex align-items-end">
                <div class="flex-grow-1">
                    <label>Amount</label>
                    <input type="number" step="0.01" class="form-control amount" name="amount[]">
                </div>
                <button type="button" class="btn btn-danger remove-item">Remove Item</button>
            </div>
        </div>`;
                $('#items').append(newItem);
            });

            $('#items').on('click', '.remove-item', function() {
                $(this).closest('.item').remove();
                calculateTotal();
            });

            $('#items').on('input', '.amount', function() {
                calculateTotal();
            });

            function calculateTotal() {
                var total = 0;
                $('.amount').each(function() {
                    var value = parseFloat($(this).val());
                    if (!isNaN(value)) total += value;
                });
                $('#total_amount').val(total.toFixed(2));
            }
        });
    </script>
</body>

</html>