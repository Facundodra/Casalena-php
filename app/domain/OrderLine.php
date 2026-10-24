<?php
// Línea de un pedido. Nombre y precio son los del momento de la compra:
// el historial no cambia aunque después cambie el ítem.

declare(strict_types=1);

class OrderLine
{
    public function __construct(
        public readonly int $id,
        public readonly int $orderId,
        public readonly int $menuItemId,
        public readonly string $itemName,
        public readonly int $quantity,
        public readonly float $unitPrice,
    ) {
    }

    public function subtotal(): float
    {
        return $this->quantity * $this->unitPrice;
    }
}
