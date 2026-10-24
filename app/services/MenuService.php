<?php
// Las reglas de la carta.

declare(strict_types=1);

class MenuService
{
    private const MAX_NAME_LENGTH = 100;

    public function __construct(private MenuItemRepository $items)
    {
    }

    /**
     * La carta agrupada por sección, en el orden de MenuSection.
     * El orden pedido aplica dentro de cada sección; las secciones vacías no aparecen.
     * $mealTime filtra por horario (almuerzo o cena), incluyendo lo de todo el día.
     *
     * @return array<int, array{section: MenuSection, items: MenuItem[]}>
     */
    public function menuBySection(string $sort = 'name', string $search = '', ?Availability $mealTime = null): array
    {
        // "Todo el día" no es un filtro: equivale a no filtrar por horario.
        if ($mealTime === Availability::ALL_DAY) {
            $mealTime = null;
        }

        $items = $this->items->findForMenu($search, $mealTime, $sort);

        $sections = [];
        foreach (MenuSection::cases() as $section) {
            $inSection = array_values(array_filter($items, fn(MenuItem $item) => $item->section() === $section));
            if ($inSection !== []) {
                $sections[] = ['section' => $section, 'items' => $inSection];
            }
        }
        return $sections;
    }

    public function detail(int $id): MenuItem
    {
        return $this->items->findById($id) ?? throw new MenuItemNotFoundException();
    }

    public function add(MenuItem $item): int
    {
        $this->validate($item);
        if ($this->items->nameExists($item->name)) {
            throw new DuplicateMenuItemException();
        }
        return $this->items->insert($item);
    }

    public function update(MenuItem $item): void
    {
        $this->validate($item);

        $current = $this->detail((int) $item->id);
        // El tipo viaja en un campo oculto, y un formulario se puede editar: se verifica acá.
        if ($current->type() !== $item->type()) {
            throw new MenuItemTypeChangeException();
        }
        if ($this->items->nameExists($item->name, $item->id)) {
            throw new DuplicateMenuItemException();
        }

        $this->items->update($item);
    }

    /** Si fue pedido alguna vez no se borra; si no, se borra y sus favoritos caen en cascada. */
    public function delete(int $id): void
    {
        $this->detail($id);
        if ($this->items->isInAnyOrder($id)) {
            throw new MenuItemInOrderException();
        }
        $this->items->delete($id);
    }

    private function validate(MenuItem $item): void
    {
        if ($item->name === '') {
            throw new InvalidDataException('El nombre no puede estar vacío.');
        }
        if (mb_strlen($item->name) > self::MAX_NAME_LENGTH) {
            throw new InvalidDataException('El nombre no puede superar los ' . self::MAX_NAME_LENGTH . ' caracteres.');
        }
        if ($item->price <= 0) {
            throw new InvalidDataException('El precio tiene que ser mayor que cero.');
        }
        if ($item instanceof Dish && $item->calories <= 0) {
            throw new InvalidDataException('Las calorías tienen que ser mayores que cero.');
        }
        if ($item instanceof Drink && $item->milliliters <= 0) {
            throw new InvalidDataException('Los mililitros tienen que ser mayores que cero.');
        }
    }
}
