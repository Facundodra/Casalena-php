<?php
declare(strict_types=1);

class MenuItemNotFoundException extends CasalenaException
{
    public function __construct()
    {
        parent::__construct('No existe ese ítem de la carta.');
    }
}
