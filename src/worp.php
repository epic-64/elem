<?php

function minimum8Characters(string $password): string {
    return strlen($password) < 8
        ? throw new InvalidArgumentException("Password must be at least 8 characters long.")
        : $password;
}

function containsNumber(string $password): string {
    return preg_match('/\d/', $password) !== 1
        ? throw new InvalidArgumentException("Password must contain at least one number.")
        : $password;
}

function containsSpecialCharacter(string $password): string {
    return preg_match('/[\W_]/', $password) !== 1
        ? throw new InvalidArgumentException("Password must contain at least one special character.")
        : $password;
}

function bcrypt(string $password): string {
    return password_hash($password, PASSWORD_BCRYPT);
}

$password = "P@ssw0rd"
    |> minimum8Characters(...)
    |> containsNumber(...)
    |> containsSpecialCharacter(...)
    |> bcrypt(...);

echo "Password is valid: " . $password . PHP_EOL;