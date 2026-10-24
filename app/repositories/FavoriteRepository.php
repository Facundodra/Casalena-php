<?php
declare(strict_types=1);

class FavoriteRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /** @return Favorite[] los más recientes primero */
    public function findByCustomer(string $email): array
    {
        $statement = $this->pdo->prepare(
            'SELECT f.id AS favorite_id, f.marked_on, m.*
             FROM favorites f
             JOIN menu_items m ON m.id = f.menu_item_id
             WHERE f.customer_email = ?
             ORDER BY f.marked_on DESC, m.name'
        );
        $statement->execute([$email]);

        $favorites = [];
        foreach ($statement->fetchAll() as $row) {
            $favorites[] = new Favorite(
                (int) $row['favorite_id'],
                $email,
                MenuItemMapper::fromRow($row),
                $row['marked_on'],
            );
        }
        return $favorites;
    }

    /** @return int[] los ids de los ítems que el cliente tiene en favoritos */
    public function findMenuItemIdsByCustomer(string $email): array
    {
        $statement = $this->pdo->prepare('SELECT menu_item_id FROM favorites WHERE customer_email = ?');
        $statement->execute([$email]);
        return array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
    }

    public function exists(string $email, int $menuItemId): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT 1 FROM favorites WHERE customer_email = ? AND menu_item_id = ?'
        );
        $statement->execute([$email, $menuItemId]);
        return $statement->fetch() !== false;
    }

    public function add(string $email, int $menuItemId, string $markedOn): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO favorites (customer_email, menu_item_id, marked_on) VALUES (?, ?, ?)'
        );
        $statement->execute([$email, $menuItemId, $markedOn]);
    }

    public function remove(string $email, int $menuItemId): void
    {
        $statement = $this->pdo->prepare(
            'DELETE FROM favorites WHERE customer_email = ? AND menu_item_id = ?'
        );
        $statement->execute([$email, $menuItemId]);
    }
}
