<?php
require_once '../config/db.php';

$result = $conn->query("SELECT * FROM sales ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between mb-4">
        <h2>Sales History</h2>
        <a href="../dashboard.php" class="btn btn-secondary">← Dashboard</a>
    </div>

    <div class="card p-3">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

            <?php if($result->num_rows > 0): ?>

                <?php while($row = $result->fetch_assoc()): ?>

                <tr>
                    <td><?= $row['id']; ?></td>
                    <td><?= $row['invoice_no']; ?></td>
                    <td><?= $row['sale_date']; ?></td>
                    <td>₹<?= number_format($row['total_amount'],2); ?></td>

                    <td>
                        <a href="../billing/invoice.php?id=<?= $row['id']; ?>"
                           class="btn btn-primary btn-sm">
                           View Invoice
                        </a>
                    </td>
                </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5" class="text-center">
                        No invoices found.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>