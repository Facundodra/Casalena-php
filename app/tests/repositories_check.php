<?php
// Verificación de los repositorios contra la base real (sin navegador).
//     docker compose exec web php /var/www/app/tests/repositories_check.php
// Crea datos de prueba con prefijo "__test" y los borra al final.

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

function rejectedByDatabase(callable $action): bool
{
    try {
        $action();
        return false;
    } catch (PDOException) {
        return true;
    }
}

$pdo       = connection();
$users     = new UserRepository($pdo);
$items     = new MenuItemRepository($pdo);
$favorites = new FavoriteRepository($pdo);
$orders    = new OrderRepository($pdo);

$email = '__test@casalena.uy';

function cleanUp(PDO $pdo, string $email): void
{
    $pdo->prepare('DELETE FROM orders WHERE customer_email = ?')->execute([$email]);
    $pdo->prepare('DELETE FROM favorites WHERE customer_email = ?')->execute([$email]);
    $pdo->exec("DELETE FROM menu_items WHERE name LIKE '\\_\\_test%'");
    $pdo->prepare('DELETE FROM users WHERE email = ?')->execute([$email]);
}

echo "Verificación de repositorios\n";
cleanUp($pdo, $email);

// --- Usuarios -------------------------------------------------------------
$users->save(new Customer($email, 'Cliente Test', password_hash('test123', PASSWORD_DEFAULT), 'Rambla 1234'));
$found = $users->findByEmail($email);
check($found instanceof Customer && $found->address === 'Rambla 1234', 'Guarda y recupera un Customer');
check($users->emailExists($email), 'emailExists encuentra el email');
check(rejectedByDatabase(fn() => $pdo->exec(
    "INSERT INTO users (email, name, password, type, job_title, address)
     VALUES ('__x@x', 'X', 'x', 'CUSTOMER', 'Chef', NULL)"
)), 'La base rechaza un cliente sin dirección y con cargo (CHECK de subtipo)');

// --- Carta ------------------------------------------------------------------
$dishId  = $items->insert(new Dish(null, '__test Milanesa', 450, Availability::LUNCH, Category::MAIN, 800));
$drinkId = $items->insert(new Drink(null, '__test Cerveza', 200, Availability::ALL_DAY, true, 500));

$dish = $items->findById($dishId);
check($dish instanceof Dish && $dish->category === Category::MAIN, 'Guarda y recupera un Dish con su categoría');
check($items->findById($drinkId)?->section() === MenuSection::ALCOHOLIC_DRINKS, 'Una bebida alcohólica va a su sección');
check(count($items->findForMenu('__TEST MILA')) === 1, 'La búsqueda no distingue mayúsculas');
check(count($items->findForMenu('__test', Availability::DINNER)) === 1, 'Filtro cena: trae la cerveza (todo el día), no la milanesa (almuerzo)');
check($items->nameExists('__test Milanesa') && !$items->nameExists('__test Milanesa', $dishId),
    'nameExists respeta el ítem que se está editando');

$items->update(new Dish($dishId, '__test Milanesa', 520, Availability::LUNCH, Category::MAIN, 800));
check($items->findById($dishId)->price === 520.0, 'update cambia el precio');

check(rejectedByDatabase(fn() => $pdo->exec(
    "INSERT INTO menu_items (name, price, availability, type, category, calories, alcoholic, milliliters)
     VALUES ('__test Mal', 100, 'LUNCH', 'DRINK', 'MAIN', 300, 0, 500)"
)), 'La base rechaza una bebida con categoría y calorías (CHECK de subtipo)');
check(rejectedByDatabase(fn() => $items->insert(
    new Dish(null, '__test Milanesa', 1, Availability::LUNCH, Category::MAIN, 1)
)), 'La base rechaza un nombre repetido (UNIQUE)');

// --- Favoritos --------------------------------------------------------------
$favorites->add($email, $drinkId, date('Y-m-d'));
check($favorites->exists($email, $drinkId), 'Agrega un favorito');
check(rejectedByDatabase(fn() => $favorites->add($email, $drinkId, date('Y-m-d'))),
    'La base rechaza el mismo favorito dos veces (UNIQUE)');
check(count($favorites->findByCustomer($email)) === 1, 'findByCustomer trae el favorito con su ítem');

// --- Pedidos ----------------------------------------------------------------
$orderId = $orders->create($email, DeliveryMode::DELIVERY, [
    ['item' => $items->findById($dishId),  'quantity' => 2],
    ['item' => $items->findById($drinkId), 'quantity' => 1],
]);
check($orderId > 0, 'Crea un pedido con dos líneas');

$items->update(new Dish($dishId, '__test Milanesa napolitana', 600, Availability::LUNCH, Category::MAIN, 900));
$order = $orders->findByCustomer($email)[0];
check($order->lines[0]->itemName === '__test Milanesa' && $order->lines[0]->unitPrice === 520.0,
    'El historial conserva el nombre y el precio del momento de la compra');
check($order->total() === 1240.0, 'El total del pedido es 2 × 520 + 200 = 1240');

check($items->isInAnyOrder($dishId), 'isInAnyOrder detecta un ítem pedido');
check(rejectedByDatabase(fn() => $items->delete($dishId)),
    'La base impide borrar un ítem que está en un pedido (RESTRICT)');

// --- Cascada de favoritos -----------------------------------------------------
$softId = $items->insert(new Drink(null, '__test Limonada', 150, Availability::ALL_DAY, false, 400));
$favorites->add($email, $softId, date('Y-m-d'));
$items->delete($softId);
check(!$favorites->exists($email, $softId), 'Al borrar un ítem no pedido, sus favoritos caen (CASCADE)');

cleanUp($pdo, $email);
echo "\nTODO OK — $step verificaciones. Los datos de prueba se borraron.\n";
