<?php
// Prueba de humo: ejercita las reglas de negocio sobre los datos de ejemplo.
//     docker compose exec web php /var/www/app/tests/smoke_test.php
// Requiere haber corrido data/seed.php. Deja la base como la encontró.

declare(strict_types=1);

error_reporting(E_ALL);

require __DIR__ . '/../settings.php';
require __DIR__ . '/../loader.php';
require __DIR__ . '/../connection.php';

$step = 0;

function check(bool $condition, string $message): void
{
    global $step;
    $step++;
    if (!$condition) {
        echo "  ✘ $step. FALLÓ: $message\n";
        exit(1);
    }
    echo "  ✔ $step. $message\n";
}

/** Verifica que $action lance exactamente la excepción esperada. */
function mustThrow(string $expected, callable $action, string $message): void
{
    try {
        $action();
        check(false, "$message (no lanzó nada)");
    } catch (CasalenaException $e) {
        check($e instanceof $expected, $e instanceof $expected ? $message : "$message (lanzó " . $e::class . ')');
    }
}

$pdo            = connection();
$itemRepository = new MenuItemRepository($pdo);
$users          = new UserService(new UserRepository($pdo));
$menu           = new MenuService($itemRepository);
$favorites      = new FavoriteService(new FavoriteRepository($pdo), $itemRepository);
$orders         = new OrderService(new OrderRepository($pdo), $itemRepository);

$idOf = function (string $name) use ($pdo): int {
    $statement = $pdo->prepare('SELECT id FROM menu_items WHERE name = ?');
    $statement->execute([$name]);
    return (int) $statement->fetchColumn();
};
$count = fn(string $table): int => (int) $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();

echo "Prueba de humo de Casaleña\n";

// --- Datos de ejemplo -----------------------------------------------------------
check($count('users') === 3 && $count('menu_items') === 16, 'Hay 3 usuarios y 16 ítems precargados');

// --- Usuarios -------------------------------------------------------------------
check($users->login('admin@casalena.uy', 'admin123') instanceof Admin, 'El login del admin devuelve un Admin');
mustThrow(InvalidCredentialsException::class,
    fn() => $users->login('ana@mail.com', 'incorrecta'),
    'Contraseña incorrecta: InvalidCredentialsException');
mustThrow(DuplicateEmailException::class,
    fn() => $users->register(Customer::TYPE, 'ana@mail.com', 'Otra Ana', 'secreta1', 'Calle 1'),
    'Email repetido: DuplicateEmailException');
mustThrow(InvalidDataException::class,
    fn() => $users->register(Customer::TYPE, 'nueva@mail.com', 'Nueva', '123', 'Calle 1'),
    'Contraseña corta: InvalidDataException');

// --- Carta: secciones, orden y búsqueda ----------------------------------------------
$sections = $menu->menuBySection();
check(count($sections) === 5 && $sections[0]['section'] === MenuSection::STARTERS
    && $sections[4]['section'] === MenuSection::ALCOHOLIC_DRINKS,
    'La carta tiene 5 secciones, de Entradas a Bebidas con alcohol');

$mainsByName  = array_map(fn($i) => $i->name, $menu->menuBySection('name')[1]['items']);
$mainsByPrice = array_map(fn($i) => $i->name, $menu->menuBySection('price')[1]['items']);
check($mainsByName[0] === 'Asado de tira' && $mainsByPrice[0] === 'Ñoquis caseros',
    'Principales: por nombre primero Asado, por precio primero Ñoquis');

$found = $menu->menuBySection('name', 'DULCE');
check(count($found) === 1 && count($found[0]['items']) === 2,
    'Buscar "DULCE" encuentra 2 postres y oculta las demás secciones');

$lunchMains = array_map(fn($i) => $i->name, $menu->menuBySection('name', '', Availability::LUNCH)[1]['items']);
check(in_array('Ñoquis caseros', $lunchMains) && in_array('Milanesa a la napolitana', $lunchMains)
    && !in_array('Asado de tira', $lunchMains),
    'Filtro almuerzo: Ñoquis (almuerzo) y Milanesa (todo el día) sí; Asado (cena) no');

// --- Carta: reglas -------------------------------------------------------------------
mustThrow(DuplicateMenuItemException::class,
    fn() => $menu->add(new Dish(null, 'Provoleta', 100, Availability::LUNCH, Category::STARTER, 100)),
    'Nombre repetido: DuplicateMenuItemException');
mustThrow(InvalidDataException::class,
    fn() => $menu->add(new Drink(null, 'Jugo raro', 100, Availability::LUNCH, false, 0)),
    'Bebida con 0 ml: InvalidDataException');

$chajaId = $idOf('Chajá');
mustThrow(MenuItemTypeChangeException::class,
    fn() => $menu->update(new Drink($chajaId, 'Chajá', 290, Availability::ALL_DAY, false, 300)),
    'Cambiar el tipo al editar (plato → bebida): MenuItemTypeChangeException');
mustThrow(MenuItemInOrderException::class,
    fn() => $menu->delete($idOf('Milanesa a la napolitana')),
    'Borrar un ítem pedido: MenuItemInOrderException');

// La cascada: el Chajá está solo en favoritos; al borrarlo, su favorito cae.
$favoritesBefore = $count('favorites');
$menu->delete($chajaId);
check($count('favorites') === $favoritesBefore - 1, 'Borrar el Chajá borra su favorito en cascada');

// Se restaura para dejar la base como estaba.
$newChajaId = $menu->add(new Dish(null, 'Chajá', 290, Availability::ALL_DAY, Category::DESSERT, 510));
(new FavoriteRepository($pdo))->add('ana@mail.com', $newChajaId, date('Y-m-d', strtotime('-4 days')));
check($count('favorites') === $favoritesBefore, 'Chajá y su favorito restaurados');

// --- Favoritos -------------------------------------------------------------------------
mustThrow(DuplicateFavoriteException::class,
    fn() => $favorites->add('ana@mail.com', $newChajaId),
    'Favorito repetido: DuplicateFavoriteException');

// --- Pedidos ---------------------------------------------------------------------------
mustThrow(EmptyCartException::class,
    fn() => $orders->confirm('ana@mail.com', [], DeliveryMode::DELIVERY),
    'Carrito vacío: EmptyCartException');
mustThrow(EmptyCartException::class,
    fn() => $orders->confirm('ana@mail.com', [999999 => 1], DeliveryMode::DELIVERY),
    'Carrito con un ítem inexistente: EmptyCartException');

$history = $orders->history('ana@mail.com');
check(count($history) === 2 && $history[0]->createdAt > $history[1]->createdAt,
    'Ana tiene 2 pedidos, el más reciente primero');

$oldLine = $history[1]->lines[0];
check($oldLine->itemName === 'Milanesa a la napolitana' && $oldLine->unitPrice === 540.0
    && $menu->detail($idOf('Milanesa a la napolitana'))->price === 590.0,
    'El pedido viejo conserva la milanesa a $540; hoy vale $590');

echo "\nTODO OK — $step pruebas pasaron.\n";
