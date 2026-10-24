<?php
// Traducción de formularios a objetos del dominio.
// Acá solo se interpreta lo que llegó; las reglas de negocio las valida el servicio.

declare(strict_types=1);

const MENU_ITEM_FIELDS = [
    'type', 'name', 'price', 'availability', 'category', 'calories', 'alcoholic', 'milliliters',
];

/** Los campos del formulario de ítem tal como llegaron, para precargarlos si hay error. */
function menu_item_input(array $post): array
{
    $input = [];
    foreach (MENU_ITEM_FIELDS as $field) {
        $input[$field] = trim((string) ($post[$field] ?? ''));
    }
    // Un checkbox sin marcar no llega en el POST.
    $input['alcoholic'] = isset($post['alcoholic']) ? '1' : '';
    return $input;
}

/** Lo comparten el alta y la edición. */
function menu_item_from_form(array $post, ?int $id = null): MenuItem
{
    $input = menu_item_input($post);

    $name  = $input['name'];
    $price = (float) str_replace(',', '.', $input['price']);
    $availability = Availability::tryFrom($input['availability'])
        ?? throw new InvalidDataException('Elegí una disponibilidad válida.');

    return match ($input['type']) {
        Dish::TYPE => new Dish(
            $id, $name, $price, $availability,
            Category::tryFrom($input['category']) ?? throw new InvalidDataException('Elegí una categoría válida.'),
            (int) $input['calories'],
        ),
        Drink::TYPE => new Drink(
            $id, $name, $price, $availability,
            $input['alcoholic'] === '1',
            (int) $input['milliliters'],
        ),
        default => throw new InvalidDataException('Elegí si es un plato o una bebida.'),
    };
}

/** Los valores iniciales del formulario a partir de un ítem existente (edición). */
function menu_item_values(MenuItem $item): array
{
    return [
        'type'         => $item->type(),
        'name'         => $item->name,
        'price'        => (string) $item->price,
        'availability' => $item->availability->value,
        'category'     => $item instanceof Dish ? $item->category->value : '',
        'calories'     => $item instanceof Dish ? (string) $item->calories : '',
        'alcoholic'    => $item instanceof Drink && $item->alcoholic ? '1' : '',
        'milliliters'  => $item instanceof Drink ? (string) $item->milliliters : '',
    ];
}
