<?php
// Cerrar sesión: se olvida todo, incluido el carrito.
require __DIR__ . '/../app/config.php';

log_out();
redirect('login.php');
