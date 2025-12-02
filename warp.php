<?php

function greeting(String $name): String {
    return "Hello, " . $name . "!";
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

function getUser(): ?String {
    return rand(0, 1) ? "Alice" : null;
}

/** @noinspection PhpExpressionResultUnusedInspection */
getUser() |> maybe(greeting(...)) |> println(...);