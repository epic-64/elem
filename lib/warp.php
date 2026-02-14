<?php

/**
 * @template-covariant T
 * @template-covariant E of Exception
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
 * @param callable(): T $callback
 * @return Attempt<T, Exception>
 */
function attempt(callable $callback): Attempt {
    try {
        return success($callback());
    } catch (Exception $e) {
        return failure($e);
    }
}

/**
 * @template T
 * @template U
 * @param callable(T): U $c
 * @return callable(Attempt<T, Exception>): Attempt<U, Exception>
 */
function liftAttempt(callable $c): callable
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
    |> liftAttempt(greeting(...))
    |> liftAttempt(fn($name) => strtoupper($name))
    |> liftAttempt(fn($greet) => $greet . " Have a great day!");

match (get_class($greeting)) {
    Success::class => println($greeting->value),
    Failure::class => println("Error: " . $greeting->exception->getMessage()),
    default => throw new InvalidArgumentException("Invalid Attempt type"),
};
