<?php
// Datos mal cargados en un formulario (campo vacío, precio no positivo, email inválido...).
// El mensaje lo pone quien la lanza, porque dice qué dato está mal.

declare(strict_types=1);

class InvalidDataException extends CasalenaException
{
}
