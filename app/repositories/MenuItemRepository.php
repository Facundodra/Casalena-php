<?php
declare(strict_types=1);

class MenuItemRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    /**
     * Los ítems de la carta, con filtros opcionales:
     * - $search: parte del nombre, sin distinguir mayúsculas.
     * - $mealTime: LUNCH o DINNER; incluye también los de ALL_DAY.
     *
     * Al SQL solo se le agregan fragmentos fijos escritos acá; los datos del
     * usuario viajan siempre como parámetros (?).
     *
     * @return MenuItem[]
     */
    public function findForMenu(string $search = '', ?Availability $mealTime = null, string $sort = 'name'): array
    {
        $conditions = [];
        $params = [];

        if ($search !== '') {
            $conditions[] = 'LOWER(name) LIKE LOWER(?)';
            $params[] = '%' . $search . '%';
        }

        if ($mealTime !== null) {
            $conditions[] = 'availability IN (?, ?)';
            $params[] = $mealTime->value;
            $params[] = Availability::ALL_DAY->value;
        }

        $where = $conditions === [] ? '' : 'WHERE ' . implode(' AND ', $conditions);

        $statement = $this->pdo->prepare("SELECT * FROM menu_items $where " . $this->orderBy($sort));
        $statement->execute($params);
        return array_map(MenuItemMapper::fromRow(...), $statement->fetchAll());
    }

    public function findById(int $id): ?MenuItem
    {
        $statement = $this->pdo->prepare('SELECT * FROM menu_items WHERE id = ?');
        $statement->execute([$id]);

        $row = $statement->fetch();
        return $row === false ? null : MenuItemMapper::fromRow($row);
    }

    /** Con $exceptId, ignora ese ítem: para editar sin chocar consigo mismo. */
    public function nameExists(string $name, ?int $exceptId = null): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM menu_items WHERE name = ? AND id <> ?');
        $statement->execute([$name, $exceptId ?? 0]);
        return $statement->fetch() !== false;
    }

    public function insert(MenuItem $item): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO menu_items
                 (name, price, availability, type, category, calories, alcoholic, milliliters)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $statement->execute(MenuItemMapper::toColumns($item));

        return (int) $this->pdo->lastInsertId();
    }

    public function update(MenuItem $item): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE menu_items
             SET name = ?, price = ?, availability = ?, type = ?,
                 category = ?, calories = ?, alcoholic = ?, milliliters = ?
             WHERE id = ?'
        );
        $statement->execute([...MenuItemMapper::toColumns($item), $item->id]);
    }

    /** Los favoritos del ítem caen solos: la FK tiene ON DELETE CASCADE. */
    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM menu_items WHERE id = ?');
        $statement->execute([$id]);
    }

    public function isInAnyOrder(int $id): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM order_lines WHERE menu_item_id = ? LIMIT 1');
        $statement->execute([$id]);
        return $statement->fetch() !== false;
    }

    /**
     * Un nombre de columna no puede ser parámetro de un prepared statement,
     * así que el ORDER BY sale de una lista blanca: nunca del texto del usuario.
     */
    private function orderBy(string $sort): string
    {
        return $sort === 'price' ? 'ORDER BY price, name' : 'ORDER BY name';
    }
}
