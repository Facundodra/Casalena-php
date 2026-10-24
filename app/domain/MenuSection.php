<?php
// Las secciones de la carta. No se guarda en la base: se deduce de cada ítem
// (la categoría si es plato, si tiene alcohol si es bebida).
// El orden de los case es el orden en que se muestran.

declare(strict_types=1);

enum MenuSection
{
    case STARTERS;
    case MAINS;
    case DESSERTS;
    case SOFT_DRINKS;
    case ALCOHOLIC_DRINKS;

    public function title(): string
    {
        return match ($this) {
            MenuSection::STARTERS         => 'Entradas',
            MenuSection::MAINS            => 'Principales',
            MenuSection::DESSERTS         => 'Postres',
            MenuSection::SOFT_DRINKS      => 'Bebidas sin alcohol',
            MenuSection::ALCOHOLIC_DRINKS => 'Bebidas con alcohol',
        };
    }
}
