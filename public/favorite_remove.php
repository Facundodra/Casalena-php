<?php
// Quitar un ítem de favoritos. Solo clientes y solo por POST.
require __DIR__ . '/../app/config.php';
require_customer();
require_post('favorites.php');

favorite_service()->remove(current_user()['email'], (int) ($_POST['menu_item_id'] ?? 0));
flash('success', 'Quitado de tus favoritos.');

redirect(safe_return_path($_POST['return'] ?? null, 'favorites.php'));
