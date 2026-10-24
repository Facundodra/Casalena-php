<?php
// Agregar al carrito. Solo clientes y solo por POST.
// No toca la base de pedidos: el carrito vive en la sesión.
require __DIR__ . '/../app/config.php';
require_customer();
require_post('menu.php');

$menuItemId = (int) ($_POST['menu_item_id'] ?? 0);
$quantity   = max(1, (int) ($_POST['quantity'] ?? 1));

try {
    $item = menu_service()->detail($menuItemId);   // que exista antes de agregarlo
    cart_add($item->id, $quantity);
    flash('success', $item->name . ' agregado al carrito.');
} catch (CasalenaException $e) {
    flash('error', $e->getMessage());
}

redirect(safe_return_path($_POST['return'] ?? null));
