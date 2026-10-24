<?php
declare(strict_types=1);

class Customer extends User
{
    public const TYPE = 'CUSTOMER';

    public function __construct(
        string $email,
        string $name,
        string $password,
        public readonly string $address,
    ) {
        parent::__construct($email, $name, $password);
    }

    public function type(): string
    {
        return self::TYPE;
    }
}
