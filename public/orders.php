<?php
// El historial de pedidos del cliente, el más reciente primero.
require __DIR__ . '/../app/config.php';
require_customer();

render('orders', [
    'title'  => 'Mis pedidos',
    'orders' => order_service()->history(current_user()['email']),
]);
