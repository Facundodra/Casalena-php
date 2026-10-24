<?php
// Cambia el tema claro/oscuro sin JavaScript: se guarda en una cookie y se vuelve
// a la página donde estaba. La cookie dura un año y sobrevive al cierre de sesión.
require __DIR__ . '/../app/config.php';
require_post('menu.php');

$theme = $_POST['theme'] ?? '';

if (in_array($theme, THEMES, true)) {
    setcookie('theme', $theme, [
        'expires'  => time() + 60 * 60 * 24 * 365,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

redirect(safe_return_path($_POST['return'] ?? null));
