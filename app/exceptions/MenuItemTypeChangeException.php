<?php
declare(strict_types=1);

class MenuItemTypeChangeException extends CasalenaException
{
    public function __construct()
    {
        parent::__construct('No se puede cambiar el tipo de un ítem: un plato no se convierte en bebida.');
    }
}
