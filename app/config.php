<?php
// Punto de entrada de toda página de public/:
//     require __DIR__ . '/../app/config.php';

declare(strict_types=1);

error_reporting(E_ALL);

// Cookie de sesión inaccesible desde JavaScript y no enviada desde otros sitios.
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

require_once __DIR__ . '/settings.php';
require_once __DIR__ . '/loader.php';
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/services.php';
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/view.php';
require_once __DIR__ . '/forms.php';
