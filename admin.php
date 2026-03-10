<?php
session_start();
require_once('db.php');

if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['is_admin'])) {
    header('Location: avtoris.php');
    exit;
}

if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === false) {
    header('Location: index.php');
    exit;
}

if (isset($_GET['delete_user'])) {
    $user_id = (int)$_GET['delete_user'];
    if ($user_id > 0) {
        $conn->query("DELETE FROM users WHERE id = $user_id AND login != 'admin'");
    }
    header('Location: admin.php');
    exit;
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: avtoris.php');
    exit;
}

$users = [];
$sql = "SELECT id, email, login, pass FROM users ORDER BY id DESC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
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
            display: flex;
            gap: 10px;
        }
        
        .admin-menu a {
            background: #f5f5f5;
            color: #2e2a21;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th {
            background: #677964;
            color: white;
            padding: 10px;
            text-align: left;
        }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .btn {
            background: #2e2a21;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            display: inline-block;
        }
        
        .btn-small {
            padding: 5px 10px;
            font-size: 14px;
            margin: 0 2px;
        }
        
        .btn-delete {
            background: #677964;
        }
        
        .btn-delete:hover {
            background: #677964;
        }
        
        .admin-badge {
            background: #ecde8e;
            color: #2e2a21;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
        }
        
        .warning {
            color: #dc3545;
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-wrapper">
        <div class="admin-header">
            <h2>Панель администратора</h2>
            <a href="?logout=1" class="btn">Выйти</a>
        </div>

        <div class="admin-menu">
            <a href="admin.php">Главная</a>
            <a href="orders.php">Заказы</a>
            <a href="tovar_uprav.php">Товары</a>
            <a href="index.php">На сайт</a>
        </div>

        <div class="admin-content">
            <h3>📋 Пользователи (<?= count($users) ?>)</h3>
            
            <table>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Действия</th>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td>#<?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['login']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <?php if ($user['login'] == 'admin'): ?>
                            <span class="admin-badge">Администратор</span>
                        <?php else: ?>
                            Пользователь
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($user['login'] != 'admin'): ?>
                            <a href="?delete_user=<?= $user['id'] ?>" 
                               class="btn btn-small btn-delete"
                               onclick="return confirm('Удалить пользователя <?= htmlspecialchars($user['login']) ?>?')">
                                Удалить
                            </a>
                        <?php else: ?>
                            <span style="color: #999;">Нельзя удалить</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <p style="margin-top: 20px; color: #677964; font-size: 14px;">
                ⚠️ Нельзя удалить главного администратора (admin)
            </p>
            <p style="color: #677964; font-size: 14px;">
                Вход для админа: admin / admin
            </p>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>