<?php
/** @var string $title */
/** @var array|null $flash */
$user  = current_user();
$theme = current_theme();
?>
<!DOCTYPE html>
<html lang="es"<?= $theme !== null ? ' data-theme="' . e($theme) . '"' : '' ?>>
<head>
    <meta charset="utf-8">
    <!-- Sin esta línea el teléfono simula una pantalla ancha y el @media no actúa -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title) ?> · Casaleña</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header class="site-header">
    <!-- La marca es el link a la carta -->
    <a class="brand" href="menu.php" title="Ir a la carta">Casaleña</a>

    <nav class="main-nav">
        <?php if (is_admin()): ?>
            <a href="item_new.php"><?= icon('plus') ?>Nuevo ítem</a>
        <?php endif; ?>

        <?php if (is_customer()): ?>
            <a href="favorites.php"><?= icon('heart') ?>Favoritos</a>
            <a href="cart.php"><?= icon('cart') ?>Carrito
                <?php if (cart_count() > 0): ?><span class="badge"><?= cart_count() ?></span><?php endif; ?>
            </a>
            <a href="orders.php"><?= icon('clock') ?>Mis pedidos</a>
        <?php endif; ?>
    </nav>

    <div class="user-area">
        <?php if ($user === null): ?>
            <a class="button secondary small" href="login.php"><?= icon('login') ?>Entrar</a>
        <?php else: ?>
            <span class="user-name"><?= icon('user') ?><?= e($user['name']) ?></span>
            <a class="button secondary small" href="logout.php"><?= icon('logout') ?>Salir</a>
        <?php endif; ?>

        <!-- Sin JavaScript: el CSS muestra solo el botón del tema contrario al actual -->
        <form class="theme-switch" method="post" action="theme.php">
            <input type="hidden" name="return" value="<?= e($_SERVER['REQUEST_URI']) ?>">
            <button class="to-dark" type="submit" name="theme" value="dark" title="Tema oscuro" aria-label="Tema oscuro">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20.5 14.5A8.5 8.5 0 0 1 9.5 3.5a8.5 8.5 0 1 0 11 11Z"/>
                </svg>
            </button>
            <button class="to-light" type="submit" name="theme" value="light" title="Tema claro" aria-label="Tema claro">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="4"/>
                    <path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>
                </svg>
            </button>
        </form>
    </div>
</header>

<?php if ($flash !== null): ?>
    <p class="flash flash-<?= e($flash['type']) ?>" role="status"><?= e($flash['message']) ?></p>
<?php endif; ?>

<main>
