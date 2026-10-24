<?php
// El pedido con sus líneas: la composición del modelo.

declare(strict_types=1);

class Order
{
    /** @param OrderLine[] $lines */
    public function __construct(
        public readonly int $id,
        public readonly string $customerEmail,
        public readonly string $createdAt,  // 'YYYY-MM-DD HH:MM:SS'
        public readonly DeliveryMode $deliveryMode,
        public readonly array $lines,
    ) {
    }

    public function total(): float
    {
        $total = 0.0;
        foreach ($this->lines as $line) {
            $total += $line->subtotal();
        }
        return $total;
    }
}
