<?php
// Edición de un ítem (?id=). Solo administradores. El tipo no se cambia.
require __DIR__ . '/../app/config.php';
require_admin();

if (is_post()) {
    $id = (int) ($_POST['id'] ?? 0);

    try {
        menu_service()->update(menu_item_from_form($_POST, $id));
        flash('success', 'Ítem actualizado.');
        redirect('menu.php');
    } catch (CasalenaException $e) {
        flash('error', $e->getMessage());
        keep_input(menu_item_input($_POST));
        redirect('item_edit.php?id=' . $id);
    }
}

try {
    $item = menu_service()->detail((int) ($_GET['id'] ?? 0));
} catch (CasalenaException $e) {
    flash('error', $e->getMessage());
    redirect('menu.php');
}

render('item_form', [
    'title'   => 'Editar ítem',
    'action'  => 'item_edit.php',
    'editing' => true,
    'id'      => $item->id,
    'values'  => menu_item_values($item),
]);
