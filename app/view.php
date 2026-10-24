<?php
// Mostrar vistas y formatear datos para la pantalla.

declare(strict_types=1);

const THEMES = ['light', 'dark'];

/**
 * Muestra una vista dentro del layout (cabecera + vista + pie).
 * Las claves de $data llegan a la vista como variables: ['items' => ...] → $items.
 */
function render(string $view, array $data = []): void
{
    $data['title'] ??= 'Casaleña';
    $data['old']   ??= take_input();
    $data['flash'] = take_flash();

    extract($data, EXTR_SKIP);

    require __DIR__ . '/views/partials/header.php';
    require __DIR__ . '/views/' . $view . '.php';
    require __DIR__ . '/views/partials/footer.php';
}

/** Escape de HTML: TODA salida dinámica pasa por acá (evita XSS). */
function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money(float $amount): string
{
    return '$ ' . number_format($amount, 2, ',', '.');
}

/** Íconos SVG de línea, todos con el mismo trazo. Heredan el color del texto. */
function icon(string $name): string
{
    $paths = [
        'back'   => '<path d="M15 18l-6-6 6-6"/>',
        'next'   => '<path d="M9 18l6-6-6-6"/>',
        'plus'   => '<path d="M12 5v14M5 12h14"/>',
        'edit'   => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/>',
        'trash'  => '<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/>',
        'check'  => '<path d="M20 6 9 17l-5-5"/>',
        'close'  => '<path d="M18 6 6 18M6 6l12 12"/>',
        'login'  => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5M15 12H3"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/>',
        'user'   => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'filter' => '<path d="M3 5h18l-7 8v6l-4 2v-8Z"/>',
        'heart'  => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8Z"/>',
        'cart'   => '<circle cx="9" cy="20" r="1.5"/><circle cx="18" cy="20" r="1.5"/><path d="M2 3h3l2.7 12.4a2 2 0 0 0 2 1.6h8.6a2 2 0 0 0 2-1.6L22 7H6"/>',
        'clock'  => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'minus'  => '<path d="M5 12h14"/>',
    ];

    return '<svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" '
        . 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths[$name] . '</svg>';
}

/** Botón "volver", igual en todas las páginas internas. */
function back_link(string $href = 'menu.php', string $label = 'Volver a la carta'): string
{
    return '<a class="button secondary small back-link" href="' . e($href) . '">'
        . icon('back') . e($label) . '</a>';
}

/** Los datos propios del subtipo, en una línea: "980 kcal" o "500 ml · con alcohol". */
function item_details(MenuItem $item): string
{
    return match (true) {
        $item instanceof Dish  => $item->calories . ' kcal',
        $item instanceof Drink => $item->milliliters . ' ml · ' . ($item->alcoholic ? 'con alcohol' : 'sin alcohol'),
    };
}

function format_date(string $dateTime, bool $withTime = false): string
{
    return date($withTime ? 'd/m/Y H:i' : 'd/m/Y', strtotime($dateTime));
}

/** El tema elegido (cookie), o null si nunca eligió: entonces manda el del sistema. */
function current_theme(): ?string
{
    $theme = $_COOKIE['theme'] ?? null;
    return in_array($theme, THEMES, true) ? $theme : null;
}
