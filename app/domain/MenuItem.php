<?php
// Raíz abstracta de la carta: cada fila de `menu_items` es un Dish o un Drink.

declare(strict_types=1);

abstract class MenuItem
{
    public function __construct(
        public readonly ?int $id,           // null hasta que la base le asigna uno
        public readonly string $name,
        public readonly float $price,
        public readonly Availability $availability,
    ) {
    }

    /** Valor de la columna discriminadora `type`. */
    abstract public function type(): string;

    abstract public function typeLabel(): string;

    abstract public function section(): MenuSection;
}
