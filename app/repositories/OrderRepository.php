<?php
declare(strict_types=1);

class OrderRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Crea el pedido con todas sus líneas en UNA transacción:
     * si cualquier INSERT falla, el rollBack deshace todo.
     *
     * $createdAt solo lo usan los datos de ejemplo, para fechar pedidos en el pasado.
     *
     * @param array<int, array{item: MenuItem, quantity: int}> $lines
     */
    public function create(
        string $customerEmail,
        DeliveryMode $deliveryMode,
        array $lines,
        ?string $createdAt = null,
    ): int {
        $this->pdo->beginTransaction();

        try {
            $orderStatement = $this->pdo->prepare(
                'INSERT INTO orders (customer_email, created_at, delivery_mode)
                 VALUES (?, COALESCE(?, NOW()), ?)'
            );
            $orderStatement->execute([$customerEmail, $createdAt, $deliveryMode->value]);
            $orderId = (int) $this->pdo->lastInsertId();

            $lineStatement = $this->pdo->prepare(
                'INSERT INTO order_lines (order_id, menu_item_id, item_name, quantity, unit_price)
                 VALUES (?, ?, ?, ?, ?)'
            );
            foreach ($lines as $line) {
                // Nombre y precio se congelan acá: los de este momento.
                $lineStatement->execute([
                    $orderId,
                    $line['item']->id,
                    $line['item']->name,
                    $line['quantity'],
                    $line['item']->price,
                ]);
            }

            $this->pdo->commit();
            return $orderId;

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    /** @return Order[] los más recientes primero */
    public function findByCustomer(string $email): array
    {
        $statement = $this->pdo->prepare(
            'SELECT o.id AS order_id, o.created_at, o.delivery_mode,
                    l.id AS line_id, l.menu_item_id, l.item_name, l.quantity, l.unit_price
             FROM orders o
             JOIN order_lines l ON l.order_id = o.id
             WHERE o.customer_email = ?
             ORDER BY o.created_at DESC, o.id DESC, l.id'
        );
        $statement->execute([$email]);

        // Una fila por línea: se agrupan por pedido.
        $grouped = [];
        foreach ($statement->fetchAll() as $row) {
            $orderId = (int) $row['order_id'];
            $grouped[$orderId]['createdAt'] = $row['created_at'];
            $grouped[$orderId]['deliveryMode'] = DeliveryMode::from($row['delivery_mode']);
            $grouped[$orderId]['lines'][] = new OrderLine(
                (int) $row['line_id'],
                $orderId,
                (int) $row['menu_item_id'],
                $row['item_name'],
                (int) $row['quantity'],
                (float) $row['unit_price'],
            );
        }

        $orders = [];
        foreach ($grouped as $orderId => $data) {
            $orders[] = new Order($orderId, $email, $data['createdAt'], $data['deliveryMode'], $data['lines']);
        }
        return $orders;
    }
}
