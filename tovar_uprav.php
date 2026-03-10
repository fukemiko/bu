<?php
require_once('db.php');
session_start();

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: avtoris.php');
    exit;
}

$action = $_GET['action'] ?? 'list';

if ($action == 'add' && $_POST) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    $sql = "INSERT INTO products (name, description, price) VALUES ('$name', '$description', '$price')";
    if ($conn->query($sql) === TRUE) {
        header('Location: tovar_uprav.php');
        exit;
    } else {
        echo "Ошибка: " . $conn->error;
    }
}

if ($action == 'edit' && $_POST) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    
    $sql = "UPDATE products SET name='$name', description='$description', price='$price' WHERE id=$id";
    if ($conn->query($sql) === TRUE) {
        header('Location: tovar_uprav.php');
        exit;
    } else {
        echo "Ошибка: " . $conn->error;
    }
}

if ($action == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM products WHERE id=$id";
    $conn->query($sql);
    header('Location: tovar_uprav.php');
    exit;
}

// Получение данных для редактирования
$product = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM products WHERE id=$id");
    $product = $result->fetch_assoc();
}

// Получаем все товары
$products = [];
$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление товарами</title>
    <link rel="icon" href="img/favicon.png" type="image/png">
    <link rel="stylesheet" href="css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
        }

        main {
            flex: 1 0 auto;
            width: 100%;
        }

        .footer {
            flex-shrink: 0;
            width: 100%;
        }

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
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .admin-menu a {
            background: #f5f5f5;
            color: #2e2a21;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .admin-menu a:hover {
            background: #677964;
            color: white;
        }
        
        .admin-content {
            background: white;
            padding: 25px;
            border-radius: 10px;
        }
        
        .form-container {
            max-width: 700px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2e2a21;
            font-weight: bold;
            font-size: 15px;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .btn {
            background: #2e2a21;
            color: white;
            text-decoration: none;
            padding: 12px 30px;
            border-radius: 6px;
            display: inline-block;
            border: none;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-add {
            background: #677964;
        }
        
        .btn-edit {
            background: #677964;
        }
        
        .btn-delete {
            background: #677964;
        }
        
        .btn-small {
            padding: 8px 15px;
            font-size: 14px;
            margin: 0 2px;
        }
        
        .cancel-btn {
            background: #6c757d;
        }
        
        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }
        
        /* Стили для таблицы */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }
        
        th {
            background: #677964;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 500;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        
        tr:hover {
            background: #f5f5f5;
        }
        
        .price {
            color: #677964;
            font-weight: bold;
        }
        
        /* Заголовки секций */
        .section-title {
            margin: 30px 0 25px 0;
            color: #2e2a21;
            border-bottom: 2px solid #677964;
            padding-bottom: 10px;
            font-size: 22px;
        }
        
        /* Стили для формы */
        .form-wrapper {
            margin-top: 30px;
        }
        
        .form-wrapper form {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        
        .form-wrapper form:hover {
            box-shadow: 0 5px 20px rgba(103, 121, 100, 0.15);
        }
        
        .button-group {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }
        
        /* Пустая корзина */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        
        .empty-state p {
            font-size: 16px;
            color: #666;
            margin-bottom: 25px;
        }

        /* Дополнительные стили для форм (которые я пропустил) */
        .form-container form {
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        .form-container form:hover {
            box-shadow: 0 5px 20px rgba(103, 121, 100, 0.15);
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #677964 !important;
            outline: none;
            box-shadow: 0 0 0 3px rgba(103, 121, 100, 0.1);
        }

        .btn-add:hover,
        .btn-edit:hover {
            background: #556652 !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(103, 121, 100, 0.3);
        }

        .cancel-btn:hover {
            background: #5a6268 !important;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main>
        <div class="admin-wrapper">
            <div class="admin-header">
                <h2>Управление товарами</h2>
                <a href="admin.php" class="btn">Назад в админ панель</a>
            </div>

            <div class="admin-menu">
                <a href="tovar_uprav.php">Список товаров</a>
                <a href="tovar_uprav.php?action=add">Добавить товар</a>
            </div>

            <div class="admin-content">
                <?php if ($action == 'add'): ?>
                    <div class="form-wrapper">
                        <h3 class="section-title">Добавить новый товар</h3>
                        
                        <div class="form-container">
                            <form method="POST">
                                <div class="form-group">
                                    <label>Название товара:</label>
                                    <input type="text" name="name" required placeholder="Например: Букет Нежность">
                                </div>
                                
                                <div class="form-group">
                                    <label>Описание:</label>
                                    <textarea name="description" required placeholder="Подробное описание товара..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Цена (₽):</label>
                                    <input type="number" name="price" step="0.01" required placeholder="0.00">
                                </div>
                                
                                <div class="button-group">
                                    <button type="submit" class="btn btn-add">Сохранить товар</button>
                                    <a href="tovar_uprav.php" class="btn cancel-btn">Отмена</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php elseif ($action == 'edit' && $product): ?>
                    <!-- Форма редактирования -->
                    <div class="form-wrapper">
                        <h3 class="section-title">Редактировать товар: <?= htmlspecialchars($product['name']) ?></h3>
                        
                        <div class="form-container">
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                
                                <div class="form-group">
                                    <label>Название товара:</label>
                                    <input type="text" name="name" required value="<?= htmlspecialchars($product['name']) ?>">
                                </div>
                                
                                <div class="form-group">
                                    <label>Описание:</label>
                                    <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Цена (₽):</label>
                                    <input type="number" name="price" step="0.01" required value="<?= $product['price'] ?>">
                                </div>
                                
                                <div class="button-group">
                                    <button type="submit" class="btn btn-edit">Сохранить изменения</button>
                                    <a href="tovar_uprav.php" class="btn cancel-btn">Отмена</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Список товаров -->
                    <h3 class="section-title">Список товаров (<?= count($products) ?> шт.)</h3>
                    
                    <?php if (empty($products)): ?>
                        <div class="empty-state">
                            <p style="font-size: 18px; margin-bottom: 20px;">Товаров пока нет</p>
                            <a href="tovar_uprav.php?action=add" class="btn btn-add">Добавить первый товар</a>
                        </div>
                    <?php else: ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Название</th>
                                    <th>Описание</th>
                                    <th>Цена</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $item): ?>
                                    <tr>
                                        <td><strong>#<?= $item['id'] ?></strong></td>
                                        <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                                        <td><?= htmlspecialchars(mb_substr($item['description'], 0, 50)) ?><?= strlen($item['description']) > 50 ? '...' : '' ?></td>
                                        <td class="price"><?= number_format($item['price'], 0, '', ' ') ?> ₽</td>
                                        <td class="action-buttons">
                                            <a href="tovar_uprav.php?action=edit&id=<?= $item['id'] ?>" 
                                               class="btn btn-small btn-edit">Изменить</a>
                                            <a href="tovar_uprav.php?action=delete&id=<?= $item['id'] ?>" 
                                               class="btn btn-small btn-delete"
                                               onclick="return confirm('Удалить товар &quot;<?= htmlspecialchars($item['name']) ?>&quot;?')">Удалить</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>