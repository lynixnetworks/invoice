<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 1000px;
            margin-top: 50px;
        }

        .menu-item {
            margin-bottom: 20px;
        }

        .pagination {
            justify-content: center;
            margin-top: 20px;
        }

        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="text-center">Invoice Management</h2>
        <div class="menu-item text-center">
            <a href="invoice_form.php" class="btn btn-primary btn-lg">Create New Invoice</a>
        </div>
        <div class="menu-item text-center">
            <form action="edit_invoice.php" method="get" class="form-inline justify-content-center">
                <div class="form-group mx-sm-3">
                    <label for="invoice_id" class="sr-only">Invoice ID</label>
                    <input type="number" class="form-control" id="invoice_id" name="invoice_id" placeholder="Invoice ID" required>
                </div>
                <button type="submit" class="btn btn-warning btn-lg">Edit Invoice</button>
            </form>
        </div>

        <div class="menu-item">
            <h3 class="text-center">Browse All Invoices</h3>
            <?php
            include '../includes/database_connection.php';

            $limit = 5;
            $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($page - 1) * $limit;

            $sql_total = "SELECT COUNT(*) AS total FROM invoices";
            $result_total = $conn->query($sql_total);
            $row_total = $result_total->fetch_assoc();
            $total_invoices = $row_total['total'];
            $total_pages = ceil($total_invoices / $limit);

            $sql = "SELECT invoice_id, customer_name FROM invoices ORDER BY invoice_id LIMIT $limit OFFSET $offset";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                echo '<table class="table table-bordered table-striped">';
                echo '<thead><tr><th>Invoice ID</th><th>Customer Name</th><th>Actions</th></tr></thead>';
                echo '<tbody>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['invoice_id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['customer_name']) . '</td>';
                    echo '<td class="action-buttons">';
                    echo '<a href="edit_invoice.php?invoice_id=' . $row['invoice_id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
                    echo '<button onclick="confirmDelete(' . $row['invoice_id'] . ')" class="btn btn-sm btn-danger">Remove</button>';
                    echo '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';

                echo '<nav aria-label="Page navigation">';
                echo '<ul class="pagination">';
                if ($page > 1) {
                    echo '<li class="page-item"><a class="page-link" href="index.php?page=' . ($page - 1) . '">Previous</a></li>';
                }
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo '<li class="page-item ' . ($i == $page ? 'active' : '') . '"><a class="page-link" href="index.php?page=' . $i . '">' . $i . '</a></li>';
                }
                if ($page < $total_pages) {
                    echo '<li class="page-item"><a class="page-link" href="index.php?page=' . ($page + 1) . '">Next</a></li>';
                }
                echo '</ul></nav>';
            } else {
                echo '<p class="text-center">No invoices found.</p>';
            }

            $conn->close();
            ?>
        </div>
    </div>

    <script>
        function confirmDelete(invoice_id) {
            if (confirm("Are you sure you want to delete this invoice?")) {
                window.location.href = "delete_invoice.php?invoice_id=" + invoice_id;
            }
        }
    </script>
</body>

</html>