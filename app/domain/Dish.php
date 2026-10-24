<?php
declare(strict_types=1);

class Dish extends MenuItem
{
    public const TYPE = 'DISH';

    public function __construct(
        ?int $id,
        string $name,
        float $price,
        Availability $availability,
        public readonly Category $category,
        public readonly int $calories,
    ) {
        parent::__construct($id, $name, $price, $availability);
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function typeLabel(): string
    {
        return 'Plato';
    }

    public function section(): MenuSection
    {
        return $this->category->section();
    }
}
