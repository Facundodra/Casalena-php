<?php
// Detalle de un ítem (?id=), con los datos propios de su tipo. Público.
require __DIR__ . '/../app/config.php';

try {
    $item = menu_service()->detail((int) ($_GET['id'] ?? 0));
} catch (CasalenaException $e) {
    flash('error', $e->getMessage());
    redirect('menu.php');
}

$isFavorite = is_customer()
    && in_array($item->id, favorite_service()->favoriteIds(current_user()['email']), true);

render('item_detail', ['title' => $item->name, 'item' => $item, 'isFavorite' => $isFavorite]);
