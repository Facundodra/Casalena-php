<?php
// Cambiar la cantidad de un ítem del carrito (los botones − y +). Cantidad 0 lo quita.
require __DIR__ . '/../app/config.php';
require_customer();
require_post('cart.php');

$menuItemId = (int) ($_POST['menu_item_id'] ?? 0);

// Solo se cambian ítems que ya están en el carrito.
if (isset(cart()[$menuItemId])) {
    cart_set($menuItemId, (int) ($_POST['quantity'] ?? 0));
}

redirect('cart.php');
