<?php
declare(strict_types=1);

class DuplicateMenuItemException extends CasalenaException
{
    public function __construct()
    {
        parent::__construct('Ya existe un ítem de la carta con ese nombre.');
    }
}
