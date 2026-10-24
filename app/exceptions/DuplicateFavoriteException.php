<?php
declare(strict_types=1);

class DuplicateFavoriteException extends CasalenaException
{
    public function __construct()
    {
        parent::__construct('Ese ítem ya está en tus favoritos.');
    }
}
