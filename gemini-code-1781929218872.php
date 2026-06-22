<?php
require_once '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Fetch all inventory options for selection drop-downs
$products_query = $conn->query("SELECT * FROM products WHERE stock > 0 ORDER BY name ASC");
$products = [];
while($row = $products_query->fetch_assoc()) {
    $products[] = $row;
}

// Handle Order Placement Form submission 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invoice_no = "INV-" . rand(100000, 999999);
    $final_total = 0;
    
    // Begin structural transactions to preserve operational data states safely
    $conn->begin_transaction();

    try {
        // Create transactional base footprint record
        $stmt = $conn->prepare("INSERT INTO sales (invoice_no, total_amount) VALUES (?, ?)");
        $stmt->bind_param("sd", $invoice_no, $final_total);
        $stmt->execute();
        $sale_id = $conn->insert_id;

        // Iterate dynamically processed operational row elements
        foreach ($_POST['items'] as $item) {
            $prod_id = intval($item['product_id']);
            $qty = intval($item['quantity']);

            // Query dynamic baseline tracking info safely 
            $p_res = $conn->query("SELECT price, stock FROM products WHERE id = $prod_id")->fetch_assoc();
            $price = $p_res['price'];
            $subtotal = $price * $qty;
            $final_total += $subtotal;

            // Log individual granular structural invoice elements
            $item_stmt = $conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
            $item_stmt->bind_param("iiidd", $sale_id, $prod_id, $qty, $price, $subtotal);
            $item_stmt->execute();

            // Deduct structural stock allocations
            $conn->query("UPDATE products SET stock = stock - $qty WHERE id = $prod_id");
        }

        // Update finalized transaction footprints accurately
        $update_stmt = $conn->prepare("UPDATE sales SET total_amount = ? WHERE id = ?");
        $update_stmt->bind_param("di", $final_total, $sale_id);
        $update_stmt->execute();

        $conn->commit();
        header("Location: invoice.php?id=" . $sale_id);
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $error = "Transaction processing failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Bill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Generate Customer Bill</h2>
            <a href="../dashboard.php" class="btn btn-secondary">← Dashboard</a>
        </div>

        <form method="POST" id="billForm">
            <div class="card p-4 shadow-sm mb-4">
                <table class="table align-middle" id="billingTable">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 40%;">Select Sweet Product</th>
                            <th style="width: 20%;">Unit Price (₹)</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 20%;">Subtotal (₹)</th>
                            <th style="width: 5%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bill-row">
                            <td>
                                <select name="items[0][product_id]" class="form-select product-select" required onchange="updatePrice(this)">
                                    <option value="" data-price="0">-- Select Sweet --</option>
                                    <?php foreach($products as $p): ?>
                                        <option value="<?= $p['id']; ?>" data-price="<?= $p['price']; ?>">
                                            <?= htmlspecialchars($p['name']); ?> (Avail: <?= $p['stock']; ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td><input type="number" class="form-control price-input" readonly value="0.00"></td>
                            <td><input type="number" name="items[0][quantity]" class="form-control qty-input" min="1" value="1" required oninput="calculateRow(this)"></td>
                            <td><input type="number" class="form-control subtotal-input" readonly value="0.00"></td>
                            <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-between mt-3">
                    <button type="button" class="btn btn-outline-primary" onclick="addRow()">➕ Add Another Item</button>
                    <div class="text-end">
                        <h4>Grand Total: ₹<span id="grandTotal">0.00</span></h4>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-lg w-100">💾 Save Sale & Generate Invoice</button>
        </form>
    </div>

    <script>
    let rowIndex = 1;

    function updatePrice(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        const row = selectElement.closest('tr');
        row.querySelector('.price-input').value = parseFloat(price).toFixed(2);
        calculateRow(row.querySelector('.qty-input'));
    }

    function calculateRow(qtyInput) {
        const row = qtyInput.closest('tr');
        const price = parseFloat(row.querySelector('.price-input').value) || 0;
        const qty = parseInt(qtyInput.value) || 0;
        const subtotal = price * qty;
        row.querySelector('.subtotal-input').value = subtotal.toFixed(2);
        calculateGrandTotal();
    }

    function calculateGrandTotal() {
        let grandTotal = 0;
        document.querySelectorAll('.subtotal-input').forEach(input => {
            grandTotal += parseFloat(input.value) || 0;
        });
        document.getElementById('grandTotal').innerText = grandTotal.toFixed(2);
    }

    function addRow() {
        const tbody = document.querySelector('#billingTable tbody');
        const firstRow = tbody.querySelector('.bill-row').cloneNode(true);
        
        // Reset dynamic tracking indexes inside raw structural elements
        firstRow.querySelector('.product-select').name = `items[${rowIndex}][product_id]`;
        firstRow.querySelector('.product-select').selectedIndex = 0;
        firstRow.querySelector('.qty-input').name = `items[${rowIndex}][quantity]`;
        firstRow.querySelector('.qty-input').value = 1;
        firstRow.querySelector('.price-input').value = "0.00";
        firstRow.querySelector('.subtotal-input').value = "0.00";
        
        tbody.appendChild(firstRow);
        rowIndex++;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.bill-row');
        if(rows.length > 1) {
            btn.closest('tr').remove();
            calculateGrandTotal();
        } else {
            alert("At least one product must be included in the bill.");
        }
    }
    </script>
</body>
</html>