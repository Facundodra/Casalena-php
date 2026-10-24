<?php
declare(strict_types=1);

class FavoriteService
{
    public function __construct(
        private FavoriteRepository $favorites,
        private MenuItemRepository $items,
    ) {
    }

    /** @return Favorite[] */
    public function list(string $email): array
    {
        return $this->favorites->findByCustomer($email);
    }

    /** @return int[] */
    public function favoriteIds(string $email): array
    {
        return $this->favorites->findMenuItemIdsByCustomer($email);
    }

    public function add(string $email, int $menuItemId): void
    {
        if ($this->items->findById($menuItemId) === null) {
            throw new MenuItemNotFoundException();
        }
        if ($this->favorites->exists($email, $menuItemId)) {
            throw new DuplicateFavoriteException();
        }
        $this->favorites->add($email, $menuItemId, date('Y-m-d'));
    }

    public function remove(string $email, int $menuItemId): void
    {
        $this->favorites->remove($email, $menuItemId);
    }
}
