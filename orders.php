<?php
require_once('db.php');
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: avtoris.php');
    exit;
}

if (isset($_GET['delete'])) {
    $order_id = $_GET['delete'];
    $conn->query("DELETE FROM orders WHERE id_order = $order_id");
    header('Location: orders.php');
    exit;
}

$orders = [];
$sql = "SELECT o.*, u.login as user_login 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление заказами</title>
        <link rel="icon" href="img/favicon.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .admin-wrapper {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .admin-header {
            background: #677964;
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .admin-menu {
            background: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        
        .admin-menu a {
            background: #f5f5f5;
            color: #2e2a21;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin-right: 10px;
        }
        
        .orders-list {
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        
        .order-card {
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 20px;
            padding: 20px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            background: #f5f5f5;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        
        .order-number {
            font-weight: bold;
            color: #677964;
        }
        
        .order-items {
            margin: 15px 0;
        }
        
        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px dashed #eee;
        }
        
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            color: #677964;
            margin-top: 15px;
        }
        
        .btn {
            padding: 5px 10px;
            border-radius: 3px;
            text-decoration: none;
            color: white;
        }
        
        .btn-delete {
            background: #677964;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-wrapper">
        <div class="admin-header">
            <h2>Управление заказами</h2>
                <a href="admin.php" style="background: #2e2a21; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; margin: 10px;">Назад</a>
        </div>

        <div class="orders-list">
            <?php foreach ($orders as $order): 
                // Получаем товары для этого заказа
                $items = [];
                $items_sql = "SELECT oi.*, p.name 
                             FROM order_items oi 
                             LEFT JOIN products p ON oi.product_id = p.id 
                             WHERE oi.order_id = " . $order['id_order'];
                $items_result = $conn->query($items_sql);
                $total = 0;
            ?>
            <div class="order-card">
                <div class="order-header">
                    <span class="order-number">Заказ #<?= $order['id_order'] ?></span>
                    <span>от <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></span>
                </div>
                
                <p><strong>Пользователь:</strong> <?= htmlspecialchars($order['user_login']) ?> (ID: <?= $order['user_id'] ?>)</p>
                <p><strong>Адрес:</strong> <?= htmlspecialchars($order['address']) ?></p>
                
                <div class="order-items">
                    <strong>Состав заказа:</strong>
                    <?php 
                    if ($items_result && $items_result->num_rows > 0) {
                        while ($item = $items_result->fetch_assoc()) {
                            $sum = $item['quantity'] * $item['price'];
                            $total += $sum;
                    ?>
                    <div class="item-row">
                        <span><?= htmlspecialchars($item['name']) ?> x<?= $item['quantity'] ?></span>
                        <span><?= number_format($sum, 0, '', ' ') ?> ₽</span>
                    </div>
                    <?php 
                        }
                    } 
                    ?>
                </div>
                
                <div class="total">
                    Итого: <?= number_format($total, 0, '', ' ') ?> ₽
                </div>
                
                <div style="text-align: right; margin-top: 10px;">
                    <a href="?delete=<?= $order['id_order'] ?>" 
                       class="btn btn-delete"
                       onclick="return confirm('Удалить заказ?')">Удалить</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>