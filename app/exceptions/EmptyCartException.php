<?php
declare(strict_types=1);

class EmptyCartException extends CasalenaException
{
    public function __construct()
    {
        parent::__construct('El carrito está vacío: agregá al menos un ítem.');
    }
}
