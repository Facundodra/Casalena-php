<?php
declare(strict_types=1);

class InvalidCredentialsException extends CasalenaException
{
    public function __construct()
    {
        parent::__construct('Email o contraseña incorrectos.');
    }
}
