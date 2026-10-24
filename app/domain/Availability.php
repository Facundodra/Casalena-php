<?php
// Enumerado persistido como texto: ->value va a la base, ::from() vuelve.

declare(strict_types=1);

enum Availability: string
{
    case LUNCH   = 'LUNCH';
    case DINNER  = 'DINNER';
    case ALL_DAY = 'ALL_DAY';

    public function label(): string
    {
        return match ($this) {
            Availability::LUNCH   => 'Almuerzo',
            Availability::DINNER  => 'Cena',
            Availability::ALL_DAY => 'Todo el día',
        };
    }
}
