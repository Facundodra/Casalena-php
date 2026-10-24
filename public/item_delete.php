<?php
// Baja de un ítem. Solo administradores y solo por POST.
require __DIR__ . '/../app/config.php';
require_admin();
require_post('menu.php');

try {
    menu_service()->delete((int) ($_POST['id'] ?? 0));
    flash('success', 'Ítem eliminado de la carta.');
} catch (CasalenaException $e) {
    flash('error', $e->getMessage());
}

redirect('menu.php');
