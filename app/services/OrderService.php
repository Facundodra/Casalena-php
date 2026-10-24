<?php
// Carrito y pedidos. El carrito vive en la sesión como [menu_item_id => quantity];
// las páginas se lo pasan a este servicio tal cual.

declare(strict_types=1);

class OrderService
{
    public function __construct(
        private OrderRepository $orders,
        private MenuItemRepository $items,
    ) {
    }

    /**
     * El carrito convertido en ítems con su precio ACTUAL, para mostrarlo.
     * Los ítems que ya no existen o con cantidad inválida se omiten.
     *
     * @param array<int, int> $cart
     * @return array<int, array{item: MenuItem, quantity: int}>
     */
    public function cartLines(array $cart): array
    {
        $lines = [];
        foreach ($cart as $menuItemId => $quantity) {
            $item = $this->items->findById((int) $menuItemId);
            if ($item !== null && (int) $quantity > 0) {
                $lines[] = ['item' => $item, 'quantity' => (int) $quantity];
            }
        }
        return $lines;
    }

    /**
     * Confirma el carrito como pedido. Los precios se releen de la base:
     * jamás se usa un precio que venga del navegador.
     *
     * @param array<int, int> $cart
     */
    public function confirm(string $email, array $cart, DeliveryMode $deliveryMode): int
    {
        $lines = $this->cartLines($cart);
        if ($lines === []) {
            throw new EmptyCartException();
        }

        return $this->orders->create($email, $deliveryMode, $lines);
    }

    /** @return Order[] los más recientes primero */
    public function history(string $email): array
    {
        return $this->orders->findByCustomer($email);
    }
}
