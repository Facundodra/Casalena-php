<?php
declare(strict_types=1);

enum Category: string
{
    case STARTER = 'STARTER';
    case MAIN    = 'MAIN';
    case DESSERT = 'DESSERT';

    public function label(): string
    {
        return match ($this) {
            Category::STARTER => 'Entrada',
            Category::MAIN    => 'Principal',
            Category::DESSERT => 'Postre',
        };
    }

    public function section(): MenuSection
    {
        return match ($this) {
            Category::STARTER => MenuSection::STARTERS,
            Category::MAIN    => MenuSection::MAINS,
            Category::DESSERT => MenuSection::DESSERTS,
        };
    }
}
