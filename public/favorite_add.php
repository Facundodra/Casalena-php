<?php
// Marcar un ítem como favorito. Solo clientes y solo por POST.
require __DIR__ . '/../app/config.php';
require_customer();
require_post('menu.php');

try {
    favorite_service()->add(current_user()['email'], (int) ($_POST['menu_item_id'] ?? 0));
    flash('success', 'Agregado a tus favoritos.');
} catch (CasalenaException $e) {
    flash('error', $e->getMessage());
}

redirect(safe_return_path($_POST['return'] ?? null));
