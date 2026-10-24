<?php
// Confirmar la compra: el carrito de la sesión se convierte en un pedido, en UNA transacción.
// El formulario solo manda la modalidad de entrega: ni ítems ni precios.
require __DIR__ . '/../app/config.php';
require_customer();
require_post('cart.php');

$deliveryMode = DeliveryMode::tryFrom($_POST['delivery_mode'] ?? '');

if ($deliveryMode === null) {
    flash('error', 'Elegí cómo querés recibir el pedido.');
    redirect('cart.php');
}

try {
    $orderId = order_service()->confirm(current_user()['email'], cart(), $deliveryMode);
    cart_clear();
    flash('success', "¡Listo! Tu pedido #$orderId está confirmado.");
    redirect('orders.php');
} catch (CasalenaException $e) {
    flash('error', $e->getMessage());
    redirect('cart.php');
}
