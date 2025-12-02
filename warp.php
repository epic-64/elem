<?php

/**
 * @template-covariant T
 * @template-covariant E
 * @phpstan-sealed Success | Failure
 */
class Attempt {}

/**
 * @template-covariant T
 * @extends Attempt<T, never>
 */
class Success extends Attempt {
    /** @param T $value */
    public function __construct(public readonly mixed $value) {}
}

/**
 * @template-covariant T of Exception
 * @extends Attempt<never, T>
 */
class Failure extends Attempt {
    public function __construct(public readonly Exception $exception) {}
}

function greeting(String $name): String {
    return "Hello, " . $name . "!";
}

/**
 * @template T
 * @param T $value
 * @return Success<T>
 */
function success(mixed $value): Success {
    return new Success($value);
}

/**
 * @return Failure<Exception>
 */
function failure(Exception $exception): Failure {
    return new Failure($exception);
}

function println(mixed $input): void {
    $output = is_string($input) ? $input : var_export($input, true);
    echo $output . PHP_EOL;
}

/**
 * @template T
 * @template U
 * @param callable(T): U $c
 * @return callable(Attempt<T, mixed>): Attempt<U, mixed>
 */
function attempt(callable $c): callable
{
    /**
     * @param Attempt<T, mixed> $arg
     * @return Attempt<U, mixed>
     */
    return function (Attempt $arg) use ($c): Attempt {
        return match (get_class($arg)) {
            Success::class => success($c($arg->value)),
            Failure::class => $arg,
            default => throw new InvalidArgumentException("Invalid Attempt type"),
        };
    };
}

/**
 * @return Attempt<string, Exception>
 */
function getUser(): Attempt {
    return mt_rand(0, 1)
        ? success("Alice")
        : failure(new Exception("User not found"));
}

$greeting = getUser()
    |> attempt(greeting(...))
    |> attempt(fn($name) => strtoupper($name))
    |> attempt(fn($greet) => $greet . " Have a great day!");

match (get_class($greeting)) {
    Success::class => println($greeting->value),
    Failure::class => println("Error: " . $greeting->exception->getMessage()),
    default => throw new InvalidArgumentException("Invalid Attempt type"),
};
