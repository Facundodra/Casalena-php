<?php
// Registro y login. Los servicios no conocen $_POST ni $_SESSION:
// reciben valores, devuelven objetos o lanzan excepciones.

declare(strict_types=1);

class UserService
{
    private const MIN_PASSWORD_LENGTH = 6;

    public function __construct(private UserRepository $users)
    {
    }

    /** $extra es el cargo si es Admin, la dirección si es Customer. */
    public function register(string $type, string $email, string $name, string $password, string $extra): User
    {
        if ($email === '' || $name === '' || $password === '' || $extra === '') {
            throw new InvalidDataException('Completá todos los campos.');
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidDataException('El email no es válido.');
        }
        if (mb_strlen($password) < self::MIN_PASSWORD_LENGTH) {
            throw new InvalidDataException('La contraseña debe tener al menos ' . self::MIN_PASSWORD_LENGTH . ' caracteres.');
        }
        if ($this->users->emailExists($email)) {
            throw new DuplicateEmailException();
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $user = match ($type) {
            Admin::TYPE    => new Admin($email, $name, $hash, $extra),
            Customer::TYPE => new Customer($email, $name, $hash, $extra),
            default        => throw new InvalidDataException('Tipo de usuario inválido.'),
        };

        $this->users->save($user);
        return $user;
    }

    public function find(string $email): User
    {
        return $this->users->findByEmail($email) ?? throw new UserNotFoundException();
    }

    /** Mismo mensaje si falla el email o la contraseña: no se revela cuál de los dos acertó. */
    public function login(string $email, string $password): User
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || !password_verify($password, $user->password)) {
            throw new InvalidCredentialsException();
        }

        return $user;
    }
}
