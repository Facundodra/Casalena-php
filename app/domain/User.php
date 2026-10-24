<?php
// Raíz abstracta de la jerarquía: cada fila de `users` es un Admin o un Customer.

declare(strict_types=1);

abstract class User
{
    public function __construct(
        public readonly string $email,
        public readonly string $name,
        public readonly string $password,   // el hash, nunca la contraseña en claro
    ) {
    }

    /** Valor de la columna discriminadora `type`. */
    abstract public function type(): string;
}
