<?php
// Datos de ejemplo. Cada dato existe para probar algo (ver la tabla del README).
//     docker compose exec web php /var/www/app/data/seed.php
// Es idempotente: si ya hay usuarios, no hace nada.
// Para volver a cero: docker compose down -v && docker compose up -d

declare(strict_types=1);

error_reporting(E_ALL);

require __DIR__ . '/../settings.php';
require __DIR__ . '/../loader.php';
require __DIR__ . '/../connection.php';

$pdo = connection();
$userRepository = new UserRepository($pdo);

if ($userRepository->count() > 0) {
    echo "Ya había datos: no se tocó nada.\n";
    exit(0);
}

$itemRepository     = new MenuItemRepository($pdo);
$favoriteRepository = new FavoriteRepository($pdo);
$orderRepository    = new OrderRepository($pdo);

$userService = new UserService($userRepository);
$menuService = new MenuService($itemRepository);

function daysAgo(int $days, string $format = 'Y-m-d H:i:s'): string
{
    return date($format, strtotime("-$days days"));
}

// --- Usuarios (por el servicio: hashes reales) -------------------------------
$userService->register(Admin::TYPE,    'admin@casalena.uy', 'Laura Méndez', 'admin123', 'Encargada');
$userService->register(Customer::TYPE, 'ana@mail.com',      'Ana García',   'ana123',   'Bulevar Artigas 1234, apto 3');
$userService->register(Customer::TYPE, 'luis@mail.com',     'Luis Pérez',   'luis123',  'Rambla Gandhi 456');

// --- Carta (por el servicio) ---------------------------------------------------
// Dentro de cada sección, el orden por nombre y el orden por precio no coinciden.
$items = [
    //        nombre                            precio  disponibilidad          datos del subtipo
    new Dish(null, 'Empanadas de carne',           160, Availability::ALL_DAY, Category::STARTER, 300),
    new Dish(null, 'Provoleta',                    390, Availability::DINNER,  Category::STARTER, 450),
    new Dish(null, 'Tortilla de papas',            320, Availability::LUNCH,   Category::STARTER, 520),

    new Dish(null, 'Asado de tira',                820, Availability::DINNER,  Category::MAIN, 1100),
    new Dish(null, 'Chivito al plato',             690, Availability::ALL_DAY, Category::MAIN, 1200),
    new Dish(null, 'Milanesa a la napolitana',     590, Availability::ALL_DAY, Category::MAIN, 980),
    new Dish(null, 'Ñoquis caseros',               480, Availability::LUNCH,   Category::MAIN, 750),

    new Dish(null, 'Chajá',                        290, Availability::ALL_DAY, Category::DESSERT, 510),
    new Dish(null, 'Flan con dulce de leche',      240, Availability::ALL_DAY, Category::DESSERT, 420),
    new Dish(null, 'Panqueques con dulce de leche', 260, Availability::ALL_DAY, Category::DESSERT, 480),

    new Drink(null, 'Agua mineral',                 90, Availability::ALL_DAY, false, 500),
    new Drink(null, 'Limonada casera',             180, Availability::ALL_DAY, false, 400),
    new Drink(null, 'Refresco cola',               140, Availability::ALL_DAY, false, 600),

    new Drink(null, 'Cerveza artesanal',           260, Availability::DINNER,  true, 500),
    new Drink(null, 'Copa de Tannat',              320, Availability::DINNER,  true, 150),
    new Drink(null, 'Medio y medio',               280, Availability::ALL_DAY, true, 200),
];

$ids = [];
foreach ($items as $item) {
    $ids[$item->name] = $menuService->add($item);
}
$item = fn(string $name): MenuItem => $itemRepository->findById($ids[$name]);

// --- Favoritos ----------------------------------------------------------------
// El Chajá queda solo en favoritos (en ningún pedido): al borrarlo, su favorito cae.
$favoriteRepository->add('ana@mail.com',  $ids['Flan con dulce de leche'], daysAgo(8, 'Y-m-d'));
$favoriteRepository->add('ana@mail.com',  $ids['Chajá'],                   daysAgo(4, 'Y-m-d'));
$favoriteRepository->add('ana@mail.com',  $ids['Provoleta'],               daysAgo(1, 'Y-m-d'));
$favoriteRepository->add('luis@mail.com', $ids['Copa de Tannat'],          daysAgo(3, 'Y-m-d'));

// --- Pedidos ------------------------------------------------------------------
// La milanesa del pedido viejo de Ana se compró a $540; hoy vale $590.
$oldMilanesa = $item('Milanesa a la napolitana');
$oldMilanesa = new Dish(
    $oldMilanesa->id, $oldMilanesa->name, 540,
    $oldMilanesa->availability, $oldMilanesa->category, $oldMilanesa->calories,
);

$orderRepository->create('ana@mail.com', DeliveryMode::DELIVERY, [
    ['item' => $oldMilanesa,           'quantity' => 1],
    ['item' => $item('Agua mineral'),  'quantity' => 1],
], daysAgo(10));

$orderRepository->create('ana@mail.com', DeliveryMode::PICKUP, [
    ['item' => $item('Chivito al plato'),  'quantity' => 2],
    ['item' => $item('Cerveza artesanal'), 'quantity' => 2],
], daysAgo(2));

$orderRepository->create('luis@mail.com', DeliveryMode::DELIVERY, [
    ['item' => $item('Asado de tira'),  'quantity' => 1],
    ['item' => $item('Copa de Tannat'), 'quantity' => 1],
], daysAgo(5));

echo "Datos cargados.\n";
echo "  Admin:     admin@casalena.uy / admin123\n";
echo "  Clientes:  ana@mail.com / ana123  ·  luis@mail.com / luis123\n";
echo "  16 ítems, 4 favoritos, 3 pedidos.\n";
