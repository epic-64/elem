<?php

/**
 * @template-covariant T
 * @template-covariant E
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
 * @template-covariant T
 * @extends Attempt<never, T>
 */
class Failure extends Attempt {
    /** @param T $exception */
    public function __construct(public readonly mixed $exception) {}
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
 * @template T
 * @param T $exception
 * @return Failure<T>
 */
function failure(mixed $exception): Failure {
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
 * @return callable(T|null): (U|null)
 */
function maybe(callable $c): callable
{
    return fn(mixed $arg) => $arg === null ? null : $c($arg);
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
            Failure::class => failure($arg->exception),
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

/** @noinspection PhpExpressionResultUnusedInspection */
getUser() |> attempt(greeting(...)) |> println(...);
