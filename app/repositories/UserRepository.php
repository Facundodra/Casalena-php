<?php
declare(strict_types=1);

class UserRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?User
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = ?');
        $statement->execute([$email]);

        $row = $statement->fetch();
        return $row === false ? null : $this->fromRow($row);
    }

    public function emailExists(string $email): bool
    {
        $statement = $this->pdo->prepare('SELECT 1 FROM users WHERE email = ?');
        $statement->execute([$email]);
        return $statement->fetch() !== false;
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function save(User $user): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO users (email, name, password, type, job_title, address)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $user->email,
            $user->name,
            $user->password,
            $user->type(),
            $user instanceof Admin    ? $user->jobTitle : null,
            $user instanceof Customer ? $user->address  : null,
        ]);
    }

    private function fromRow(array $row): User
    {
        return match ($row['type']) {
            Admin::TYPE    => new Admin($row['email'], $row['name'], $row['password'], $row['job_title']),
            Customer::TYPE => new Customer($row['email'], $row['name'], $row['password'], $row['address']),
        };
    }
}
