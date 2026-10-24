<?php
declare(strict_types=1);

class Admin extends User
{
    public const TYPE = 'ADMIN';

    public function __construct(
        string $email,
        string $name,
        string $password,
        public readonly string $jobTitle,
    ) {
        parent::__construct($email, $name, $password);
    }

    public function type(): string
    {
        return self::TYPE;
    }
}
