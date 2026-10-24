<?php
// Alta de un ítem de la carta. Solo administradores.
require __DIR__ . '/../app/config.php';
require_admin();

if (is_post()) {
    try {
        menu_service()->add(menu_item_from_form($_POST));
        flash('success', 'Ítem agregado a la carta.');
        redirect('menu.php');
    } catch (CasalenaException $e) {
        flash('error', $e->getMessage());
        keep_input(menu_item_input($_POST));
        redirect('item_new.php');
    }
}

render('item_form', [
    'title'   => 'Nuevo ítem',
    'action'  => 'item_new.php',
    'editing' => false,
    'id'      => null,
    'values'  => [],
]);
