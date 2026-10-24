<?php
// Los favoritos del cliente logueado.
require __DIR__ . '/../app/config.php';
require_customer();

render('favorites', [
    'title'     => 'Mis favoritos',
    'favorites' => favorite_service()->list(current_user()['email']),
]);
