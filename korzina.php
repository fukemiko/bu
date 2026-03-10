<?php
// Подключаем функции корзины
require_once 'kor.php';
$cartItems = getCartItems();
$total = getCartTotal();
$count = getCartCount();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/korz.css">
    <link rel="icon" href="img/favicon.png" type="image/png">
    <title>Корзина - Butoshka</title>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <main>
        <div class="shopping-cart">
            <!-- Заголовок корзины -->
            <div class="cart-header">
                <h2 class="title">Корзина</h2>
                <?php if ($count > 0): ?>
                    <span class="cart-count">
                        <?php 
                        echo $count . ' '; 
                        if($count % 10 == 1 && $count % 100 != 11) echo 'товар';
                        elseif(in_array($count % 10, [2,3,4]) && !in_array($count % 100, [12,13,14])) echo 'товара';
                        else echo 'товаров';
                        ?>
                    </span>
                <?php endif; ?>
            </div>

            <?php if (empty($cartItems)): ?>
                <!-- Пустая корзина -->
                <div class="empty-cart">
                    <p>😕 Ваша корзина пока пуста</p>
                    <p style="font-size: 14px; color: #999; margin-bottom: 25px;">
                        Добавьте букеты из каталога
                    </p>
                    <a href="index.php#catalog" class="continue-shopping">
                        Перейти к покупкам
                    </a>
                </div>
            <?php else: ?>
                <!-- Товары в корзине -->
                <?php foreach ($cartItems as $item): ?>
                <div class="item" data-id="<?php echo $item['id']; ?>">
                    <!-- Кнопка удаления X -->
                    <button class="delete-btn" onclick="removeItem(<?php echo $item['id']; ?>)" title="Удалить">
                        ✕
                    </button>

                    <!-- Изображение товара -->
                    <div class="item-image">
                        <img src="img/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>"
                             onerror="this.src='img/placeholder.jpg'">
                    </div>

                    <!-- Информация о товаре -->
                    <div class="item-info">
                        <div class="item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="item-price">
                            <?php echo number_format($item['price'], 0, '', ' '); ?> ₽
                        </div>
                    </div>

                    <!-- Блок изменения количества -->
                    <div class="item-quantity">
                        <button class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, -1)">−</button>
                        <input type="text" class="qty-input" value="<?php echo $item['quantity']; ?>" readonly>
                        <button class="qty-btn" onclick="updateQuantity(<?php echo $item['id']; ?>, 1)">+</button>
                    </div>

                    <!-- Общая сумма за товар -->
                    <div class="item-total">
                        <?php echo number_format($item['price'] * $item['quantity'], 0, '', ' '); ?> ₽
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Итоговая сумма и кнопка оформления -->
                <div class="cart-summary">
                    <div class="total-amount">
                        Итого: <span><?php echo number_format($total, 0, '', ' '); ?> ₽</span>
                    </div>
                   <?php
                    // Проверяем, авторизован ли пользователь
                    session_start();
                    if (isset($_SESSION['user_id'])) {
                        echo '<a href="zakaz.php" class="checkout-btn">Оформить заказ</a>';
                    } else {
                        echo '<a href="avtoris.php" class="checkout-btn" onclick="alert(\'Для оформления заказа необходимо авторизоваться\')">Оформить заказ</a>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>

    <script>
    // Функция обновления количества
    function updateQuantity(productId, change) {
        const item = document.querySelector(`.item[data-id="${productId}"]`);
        const input = item.querySelector('.qty-input');
        let newQuantity = parseInt(input.value) + change;
        
        // Проверка на минимальное количество
        if (newQuantity < 1) {
            if (confirm('Удалить товар из корзины?')) {
                removeItem(productId);
            }
            return;
        }
        
        // Проверка на максимальное количество
        if (newQuantity > 99) {
            newQuantity = 99;
            alert('Максимальное количество - 99 шт');
        }
        
        // Отправка запроса на сервер
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('id', productId);
        formData.append('quantity', newQuantity);
        
        fetch('kor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Обновляем значение в поле
                input.value = newQuantity;
                // Перезагружаем страницу для обновления всех цен
                location.reload();
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            alert('Произошла ошибка при обновлении количества');
        });
    }
    
    // Функция удаления товара
    function removeItem(productId) {
        if (confirm('Удалить товар из корзины?')) {
            const formData = new FormData();
            formData.append('action', 'remove');
            formData.append('id', productId);
            
            fetch('kor.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Перезагружаем страницу после удаления
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Ошибка:', error);
                alert('Произошла ошибка при удалении товара');
            });
        }
    }
    </script>
</body>
</html>