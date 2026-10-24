<?php
// Madre de todas las excepciones de negocio. Nunca se lanza directamente:
// cada regla tiene su propia hija.
// Las páginas atrapan esta y muestran su mensaje como aviso. Los errores de
// programación no heredan de acá, así que no se disfrazan de mensaje amigable.

declare(strict_types=1);

class CasalenaException extends Exception
{
}
