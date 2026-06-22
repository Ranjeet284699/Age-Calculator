<?php
require_once 'config/db.php';

// Protect page from unauthorized access
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch Metrics
$total_products = $conn->query("SELECT COUNT(*) as count FROM products")->fetch_assoc()['count'];

$today = date('Y-m-d');
$today_sales_res = $conn->query("SELECT SUM(total_amount) as total FROM sales WHERE DATE(sale_date) = '$today'")->fetch_assoc();
$today_sales = $today_sales_res['total'] ?? 0;

$total_revenue_res = $conn->query("SELECT SUM(total_amount) as total FROM sales")->fetch_assoc();
$total_revenue = $total_revenue_res['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sweet Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sweet Shop Billing</a>
            <div class="d-flex">
                <span class="navbar-text me-3">Welcome, <?= htmlspecialchars($_SESSION['username']); ?></span>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col">
                <h2>Dashboard Overview</h2>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card bg-primary text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Products</h5>
                        <h2 class="card-text"><?= $total_products; ?></h2>
                        <a href="products/view_products.php" class="text-white text-decoration-none sm-text">Manage →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white h-100">
                    <div class="card-body">
                        <h5 class="card-title">Today's Sales</h5>
                        <h2 class="card-text">₹<?= number_format($today_sales, 2); ?></h2>
                        <a href="sales/sales_history.php" class="text-white text-decoration-none">View Details →</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark h-100">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <h2 class="card-text">₹<?= number_format($total_revenue, 2); ?></h2>
                        <a href="sales/sales_history.php" class="text-dark text-decoration-none">View History →</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card p-4">
            <h4>Quick Actions</h4>
            <div class="d-flex gap-2 flex-wrap mt-3">
                <a href="billing/create_bill.php" class="btn btn-lg btn-success">➕ Point of Sale (New Bill)</a>
                <a href="products/add_product.php" class="btn btn-lg btn-outline-primary">📦 Add New Sweet</a>
                <a href="sales/sales_history.php" class="btn btn-lg btn-outline-secondary">📜 View Invoices</a>
            </div>
        </div>
    </div>

</body>
</html>