<?php

// Simple debug script
$pdo = new PDO('mysql:host=localhost;dbname=pos', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== DEBUGGING AUTO-VOID RESULTS ===\n\n";

// Check voided sales
$stmt = $pdo->query("SELECT id, voided, voided_at, voided_by, total_amount FROM sales WHERE voided = 1");
$voidedSales = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Voided sales found: " . count($voidedSales) . "\n\n";

foreach ($voidedSales as $sale) {
    echo "Sale ID: {$sale['id']}, Amount: {$sale['total_amount']}, Voided at: {$sale['voided_at']}, By: {$sale['voided_by']}\n";
}

// Check credits status
echo "\n=== CREDITS STATUS ===\n";
$stmt = $pdo->query("SELECT id, reference_number, status, sale_id, date FROM credits");
$credits = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($credits as $credit) {
    echo "Credit ID: {$credit['id']}, Ref: {$credit['reference_number']}, Status: {$credit['status']}, Sale ID: {$credit['sale_id']}, Due: {$credit['date']}\n";
}

// Check stock_ins
echo "\n=== STOCK INS ===\n";
$stmt = $pdo->query("SELECT COUNT(*) as count FROM stock_ins");
$result = $stmt->fetch(PDO::FETCH_ASSOC);
echo "Total stock_ins records: {$result['count']}\n";

// Check sale_items for voided sales
echo "\n=== SALE ITEMS FOR VOIDED SALES ===\n";
foreach ($voidedSales as $sale) {
    $stmt = $pdo->prepare("SELECT product_id, quantity FROM sale_items WHERE sale_id = ?");
    $stmt->execute([$sale['id']]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Sale ID {$sale['id']} items:\n";
    foreach ($items as $item) {
        echo "  Product ID: {$item['product_id']}, Quantity: {$item['quantity']}\n";
    }
}
