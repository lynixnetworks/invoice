<?php
require 'includes/database_connection.php'; // Panggil koneksi database

// Cek apakah ada parameter 'invoice_id' di URL
if (!isset($_GET['invoice_id']) || !is_numeric($_GET['invoice_id'])) {
    echo "<div class='container mt-5'><div class='alert alert-info text-center'>Lynix Networks Payment System</div></div>";
    exit;
}

$invoice_id = intval($_GET['invoice_id']);

$sql_invoice = "SELECT * FROM invoices WHERE invoice_id = ?";
$stmt = $conn->prepare($sql_invoice);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$result_invoice = $stmt->get_result();
$invoice = $result_invoice->fetch_assoc();

if (!$invoice) {
    die("Invoice not found.");
}

$sql_items = "SELECT * FROM invoice_items WHERE invoice_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $invoice_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lynix Networks - Invoice #<?php echo $invoice['invoice_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-body">

                <div class="row">
                    <div class="col-md-6">
                        <img src="https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEi--pbPgnyis-H5N9tKYFRowQD6v3PhoDHIFHHjBZay6RL6Q-mbUJJd61DeJFpwFv1BGpjXc1or-v30SboNpTz3DdoeJskrGQG9bI-57GVgn-ZSDcfoL39pPU5POsMBxwSWZTUlP38WGzOyBCH4pEYvenJFI_H8LGMdFSIaEVfh3rymyGCTO-X0c3hMcA/s252/Lynix%20Networks.png" class="img-fluid" width="150">
                    </div>
                    <div class="col-md-6 text-end">
                        <h4 class="text-<?php echo ($invoice['status'] ? 'success' : 'danger'); ?>">
                            <?php echo ($invoice['status'] ? 'Paid' : 'Unpaid'); ?>
                        </h4>
                        <p><?php echo $invoice['payment_method']; ?> <br>
                            (<?php echo date("d/m/Y", strtotime($invoice['invoice_date'])); ?>)
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h5><strong>Invoiced To</strong></h5>
                        <p>
                            <b><?php echo $invoice['customer_name']; ?></b><br>
                            <?php echo nl2br($invoice['customer_address']); ?><br>
                            Nomor Telepon: <?php echo $invoice['customer_phone']; ?>
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h5><strong>Pay To</strong></h5>
                        <p>
                            <b><?php echo $invoice['collector_name']; ?></b><br>
                            <?php echo $invoice['collector_tagline']; ?>
                        </p>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-6">
                        <h6><strong>Invoice Date:</strong> <?php echo date("d/m/Y", strtotime($invoice['invoice_date'])); ?></h6>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6><strong>Due Date:</strong> <?php echo date("d/m/Y", strtotime($invoice['due_date'])); ?></h6>
                    </div>
                </div>

                <h4 class="text-center mt-4">Invoice #<?php echo $invoice['invoice_number']; ?></h4>

                <table class="table table-bordered mt-3">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="70%">Description</th>
                            <th width="30%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($item = $result_items->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo $item['description']; ?></td>
                                <td class="text-end">Rp.<?php echo number_format($item['amount'], 2); ?> IDR</td>
                            </tr>
                        <?php } ?>
                        <tr class="table-secondary">
                            <td class="text-end"><strong>Credit:</strong></td>
                            <td class="text-end">Rp.0.00 IDR</td>
                        </tr>
                        <tr class="table-primary">
                            <td class="text-end"><strong>Total:</strong></td>
                            <td class="text-end">Rp.<?php echo number_format($invoice['total_amount'], 2); ?> IDR</td>
                        </tr>
                    </tbody>
                </table>

                <h5 class="mt-4">Transactions</h5>
                <table class="table table-striped">
                    <thead class="table-dark text-center">
                        <tr>
                            <th width="30%">Transaction Date</th>
                            <th width="25%">Gateway</th>
                            <th width="25%">Transaction ID</th>
                            <th width="20%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center"><?php echo date("d/m/Y H:i", strtotime($invoice['transaction_date'])); ?></td>
                            <td class="text-center"><?php echo $invoice['transaction_gateway']; ?></td>
                            <td class="text-center"><?php echo $invoice['transaction_id']; ?></td>
                            <td class="text-end">Rp.<?php echo number_format($invoice['amount_paid'], 2); ?> IDR</td>
                        </tr>
                        <tr class="table-warning">
                            <td class="text-end" colspan="3"><strong>Balance:</strong></td>
                            <td class="text-end">Rp.0.00 IDR</td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-center mt-4">
                    <button class="btn btn-primary" onclick="window.print()">Print Invoice</button>
                    <a href="index.php" class="btn btn-secondary">Back</a>
                </div>

            </div>
        </div>
    </div>

</body>

</html>

<?php
$conn->close();
?>