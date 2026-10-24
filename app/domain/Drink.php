<?php
declare(strict_types=1);

class Drink extends MenuItem
{
    public const TYPE = 'DRINK';

    public function __construct(
        ?int $id,
        string $name,
        float $price,
        Availability $availability,
        public readonly bool $alcoholic,
        public readonly int $milliliters,
    ) {
        parent::__construct($id, $name, $price, $availability);
    }

    public function type(): string
    {
        return self::TYPE;
    }

    public function typeLabel(): string
    {
        return 'Bebida';
    }

    public function section(): MenuSection
    {
        return $this->alcoholic ? MenuSection::ALCOHOLIC_DRINKS : MenuSection::SOFT_DRINKS;
    }
}
