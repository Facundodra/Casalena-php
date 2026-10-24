<?php
// Clase asociativa: un cliente marca un ítem de la carta como favorito, en una fecha.

declare(strict_types=1);

class Favorite
{
    public function __construct(
        public readonly int $id,
        public readonly string $customerEmail,
        public readonly MenuItem $menuItem,
        public readonly string $markedOn,   // 'YYYY-MM-DD'
    ) {
    }
}
