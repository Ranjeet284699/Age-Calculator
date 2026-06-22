<?php
require_once '../config/db.php';

if (!isset($_GET['id'])) {
    die("Invalid Invoice ID");
}

$sale_id = intval($_GET['id']);

// Sale details
$sale_stmt = $conn->prepare("SELECT * FROM sales WHERE id = ?");
$sale_stmt->bind_param("i", $sale_id);
$sale_stmt->execute();
$sale = $sale_stmt->get_result()->fetch_assoc();

if (!$sale) {
    die("Invoice not found.");
}

// Sale items
$item_stmt = $conn->prepare("
    SELECT si.*, p.name
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    WHERE si.sale_id = ?
");
$item_stmt->bind_param("i", $sale_id);
$item_stmt->execute();
$items = $item_stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f8f9fa;
        }
        .invoice-box{
            max-width:800px;
            margin:40px auto;
            background:#fff;
            padding:30px;
            border-radius:10px;
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        }

        @media print{
            .no-print{
                display:none;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <div class="invoice-box">

        <div class="d-flex justify-content-between mb-4">
            <div>
                <h2>Sweet Shop Invoice</h2>
                <p><strong>Invoice No:</strong> <?= $sale['invoice_no']; ?></p>
                <p><strong>Date:</strong> <?= $sale['sale_date']; ?></p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-dark">
            <tr>
                <th>Product</th>
                <th>Price (₹)</th>
                <th>Quantity</th>
                <th>Subtotal (₹)</th>
            </tr>
            </thead>

            <tbody>

            <?php while($row = $items->fetch_assoc()): ?>

                <tr>
                    <td><?= htmlspecialchars($row['name']); ?></td>
                    <td><?= number_format($row['price'],2); ?></td>
                    <td><?= $row['quantity']; ?></td>
                    <td><?= number_format($row['subtotal'],2); ?></td>
                </tr>

            <?php endwhile; ?>

            </tbody>
        </table>

        <div class="text-end">
            <h3>Total: ₹<?= number_format($sale['total_amount'],2); ?></h3>
        </div>

        <div class="mt-4 no-print">
            <button class="btn btn-primary" onclick="window.print()">
                Print Invoice
            </button>

            <a href="../dashboard.php" class="btn btn-secondary">
                Back to Dashboard
            </a>
        </div>

    </div>

</div>

</body>
</html>