<?php
// El carrito del cliente, con los precios ACTUALES (el precio se congela recién al confirmar).
require __DIR__ . '/../app/config.php';
require_customer();

$lines = order_service()->cartLines(cart());

// Si un ítem se eliminó de la carta mientras estaba en el carrito, se saca de la sesión.
$validIds = array_map(fn(array $line) => $line['item']->id, $lines);
foreach (array_keys(cart()) as $menuItemId) {
    if (!in_array($menuItemId, $validIds, true)) {
        cart_set($menuItemId, 0);
    }
}

/** @var Customer $customer */
$customer = user_service()->find(current_user()['email']);

render('cart', [
    'title'    => 'Mi carrito',
    'lines'    => $lines,
    'address'  => $customer->address,
]);
