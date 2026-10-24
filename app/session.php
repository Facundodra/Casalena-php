<?php
// Todo lo que vive en la sesión: el usuario logueado, el control de acceso,
// los mensajes flash y lo que se tipeó en un formulario rechazado.

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Usuario logueado
// ---------------------------------------------------------------------------

/** @return array{email: string, name: string, type: string}|null */
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return current_user() !== null;
}

function is_admin(): bool
{
    return (current_user()['type'] ?? '') === Admin::TYPE;
}

function is_customer(): bool
{
    return (current_user()['type'] ?? '') === Customer::TYPE;
}

/** En la sesión va lo mínimo para saber quién es. Nunca la contraseña, ni su hash. */
function log_in(User $user): void
{
    // Nuevo id de sesión al loguearse: un id obtenido antes del login no sirve después.
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'email' => $user->email,
        'name'  => $user->name,
        'type'  => $user->type(),
    ];
}

function log_out(): void
{
    $_SESSION = [];
    session_destroy();
}

// ---------------------------------------------------------------------------
// Control de acceso: el ÚNICO lugar donde se decide quién entra a qué.
// Cada página protegida llama a una de estas en su primera línea.
// Esconder un botón no es seguridad: cualquiera puede escribir la URL.
// ---------------------------------------------------------------------------

function require_login(): void
{
    if (!is_logged_in()) {
        flash('error', 'Tenés que iniciar sesión para entrar ahí.');
        redirect('login.php');
    }
}

function require_admin(): void
{
    require_login();
    if (!is_admin()) {
        flash('error', 'Esa función es solo para administradores.');
        redirect('menu.php');
    }
}

function require_customer(): void
{
    require_login();
    if (!is_customer()) {
        flash('error', 'Esa función es solo para clientes.');
        redirect('menu.php');
    }
}

/** Las páginas de acción solo aceptan POST: un link o un GET nunca cambian datos. */
function require_post(string $fallback): void
{
    if (!is_post()) {
        redirect($fallback);
    }
}

// ---------------------------------------------------------------------------
// Request y redirecciones
// ---------------------------------------------------------------------------

function is_post(): bool
{
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/** Todo POST termina acá (POST-Redirect-GET): refrescar nunca reenvía un formulario. */
function redirect(string $location): never
{
    header('Location: ' . $location);
    exit;
}

/** Solo rutas internas: nunca redirigir a donde diga un formulario (ej. otro sitio). */
function safe_return_path(?string $path, string $fallback = 'menu.php'): string
{
    if ($path === null || !str_starts_with($path, '/') || str_starts_with($path, '//')) {
        return $fallback;
    }
    return $path;
}

// ---------------------------------------------------------------------------
// Mensajes flash: se guardan antes de un redirect y se muestran una sola vez.
// ---------------------------------------------------------------------------

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** @return array{type: string, message: string}|null */
function take_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// ---------------------------------------------------------------------------
// Lo tipeado en un formulario rechazado, para no hacerlo escribir de nuevo.
// Nunca se guardan contraseñas.
// ---------------------------------------------------------------------------

function keep_input(array $values): void
{
    $_SESSION['old_input'] = $values;
}

function take_input(): array
{
    $old = $_SESSION['old_input'] ?? [];
    unset($_SESSION['old_input']);
    return $old;
}

// ---------------------------------------------------------------------------
// Carrito: vive en la sesión como [menu_item_id => quantity].
// Sin precios: el precio se lee de la base al mostrarlo y al confirmar.
// ---------------------------------------------------------------------------

const CART_MAX_QUANTITY = 99;

/** @return array<int, int> */
function cart(): array
{
    return $_SESSION['cart'] ?? [];
}

/** Cantidad 0 o menos lo saca del carrito; el máximo es CART_MAX_QUANTITY. */
function cart_set(int $menuItemId, int $quantity): void
{
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$menuItemId]);
        return;
    }
    $_SESSION['cart'][$menuItemId] = min($quantity, CART_MAX_QUANTITY);
}

function cart_add(int $menuItemId, int $quantity = 1): void
{
    cart_set($menuItemId, (cart()[$menuItemId] ?? 0) + $quantity);
}

function cart_clear(): void
{
    unset($_SESSION['cart']);
}

/** Total de unidades, para el contador de la cabecera. */
function cart_count(): int
{
    return array_sum(cart());
}

