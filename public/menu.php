<?php
// La carta: pública. Parámetros GET: q=texto, meal=LUNCH|DINNER, sort=name|price.
require __DIR__ . '/../app/config.php';

$sort     = ($_GET['sort'] ?? '') === 'price' ? 'price' : 'name';
$search   = trim($_GET['q'] ?? '');
$mealTime = Availability::tryFrom($_GET['meal'] ?? '');

render('menu', [
    'title'    => 'Carta',
    'sections' => menu_service()->menuBySection($sort, $search, $mealTime),
    'sort'     => $sort,
    'search'   => $search,
    'mealTime' => $mealTime === Availability::ALL_DAY ? null : $mealTime,
    // Para marcar el corazón de los que ya son favoritos del cliente.
    'favoriteIds' => is_customer() ? favorite_service()->favoriteIds(current_user()['email']) : [],
]);
