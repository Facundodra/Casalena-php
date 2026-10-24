<?php
declare(strict_types=1);

class UserNotFoundException extends CasalenaException
{
    public function __construct()
    {
        parent::__construct('No existe ese usuario.');
    }
}
