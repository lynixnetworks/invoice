<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Form</title>
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
        <h2>Invoice Form</h2>
        <form action="submit_invoice.php" method="post">
            <div class="form-group">
                <label>Invoice Number</label>
                <input type="text" class="form-control" name="invoice_number" required>
            </div>
            <div class="form-group">
                <label>Invoice Date</label>
                <input type="date" class="form-control" name="invoice_date" required>
            </div>
            <div class="form-group">
                <label>Due Date</label>
                <input type="date" class="form-control" name="due_date" required>
            </div>
            <div class="form-group">
                <label>Customer Name</label>
                <input type="text" class="form-control" name="customer_name" required>
            </div>
            <div class="form-group">
                <label>Customer Address</label>
                <textarea class="form-control" name="customer_address"></textarea>
            </div>
            <div class="form-group">
                <label>Customer Phone</label>
                <input type="text" class="form-control" name="customer_phone">
            </div>
            <div class="form-group">
                <label>Collector Name</label>
                <input type="text" class="form-control" name="collector_name">
            </div>
            <div class="form-group">
                <label>Collector Tagline</label>
                <input type="text" class="form-control" name="collector_tagline">
            </div>
            <div class="form-group">
                <label>Collector Phone</label>
                <input type="text" class="form-control" name="collector_phone">
            </div>
            <div class="form-group">
                <label>Payment Method</label>
                <input type="text" class="form-control" name="payment_method">
            </div>
            <div class="form-group">
                <label>Transaction Date</label>
                <input type="datetime-local" class="form-control" name="transaction_date">
            </div>
            <div class="form-group">
                <label>Transaction Gateway</label>
                <input type="text" class="form-control" name="transaction_gateway">
            </div>
            <div class="form-group">
                <label>Transaction ID</label>
                <input type="text" class="form-control" name="transaction_id">
            </div>
            <div class="form-group">
                <label>Amount Paid</label>
                <input type="number" step="0.01" class="form-control" name="amount_paid">
            </div>
            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" name="status" id="status" value="1">
                <label class="form-check-label" for="status">Paid</label>
            </div>

            <div class="invoice-items-section">
                <h4>Invoice Items</h4>
                <div id="items">
                    <div class="item">
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
                    </div>
                </div>
                <button type="button" id="add-item" class="btn btn-primary">Add More Item</button>
            </div>

            <div class="form-group">
                <label>Total Amount</label>
                <input type="number" step="0.01" class="form-control" name="total_amount" id="total_amount" readonly>
            </div>

            <button type="submit" class="btn btn-success">Submit</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            // Add item
            $('#add-item').click(function() {
                var newItem = `<div class="item">
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