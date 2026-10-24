<?php
// Traduce entre filas de `menu_items` y objetos MenuItem.
// Lo comparten MenuItemRepository y FavoriteRepository (que trae el ítem con un JOIN).

declare(strict_types=1);

final class MenuItemMapper
{
    /** La columna `type` decide qué subclase construir. */
    public static function fromRow(array $row): MenuItem
    {
        $availability = Availability::from($row['availability']);

        return match ($row['type']) {
            Dish::TYPE => new Dish(
                (int) $row['id'],
                $row['name'],
                (float) $row['price'],
                $availability,
                Category::from($row['category']),
                (int) $row['calories'],
            ),
            Drink::TYPE => new Drink(
                (int) $row['id'],
                $row['name'],
                (float) $row['price'],
                $availability,
                (bool) $row['alcoholic'],
                (int) $row['milliliters'],
            ),
        };
    }

    /**
     * Valores en el orden de las columnas:
     * name, price, availability, type, category, calories, alcoholic, milliliters.
     * Las del otro subtipo van NULL.
     */
    public static function toColumns(MenuItem $item): array
    {
        return [
            $item->name,
            $item->price,
            $item->availability->value,
            $item->type(),
            $item instanceof Dish  ? $item->category->value  : null,
            $item instanceof Dish  ? $item->calories         : null,
            $item instanceof Drink ? (int) $item->alcoholic  : null,
            $item instanceof Drink ? $item->milliliters      : null,
        ];
    }
}
