<?php
declare(strict_types=1);

class MenuItemInOrderException extends CasalenaException
{
    public function __construct()
    {
        parent::__construct('No se puede eliminar: el ítem aparece en al menos un pedido.');
    }
}
