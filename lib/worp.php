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

/**
 * @template T
 * @param callable(): T $callback
 * @return T | Exception
 */
function attempt(callable $callback): mixed {
    try {
        return $callback();
    } catch (Exception $e) {
        return $e;
    }
}

function createUser(string $result): string {
    $result = attempt(fn() => $result
            |> minimum8Characters(...)
            |> containsNumber(...)
            |> containsSpecialCharacter(...)
            |> bcrypt(...));

    if ($result instanceof Exception) {
        return "Password validation failed: " . $result->getMessage();
    }

    return "User created with hashed password: " . $result;
}

function println(mixed $input): void {
    $output = is_string($input) ? $input : var_export($input, true);
    echo $output . PHP_EOL;
}

foreach (["short", "longenough", "longenough1", "LongValid1!"] as $pwd) {
    createUser($pwd) |> println(...);
}