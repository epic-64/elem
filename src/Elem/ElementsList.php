<?php

declare(strict_types=1);

namespace Epic64\Elem;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

/**
 * A simple collection class for fluent array transformations.
 * If you already have a collection library, please use that one instead.
 *
 * @template T
 * @implements IteratorAggregate<int, T>
 */
class ElementsList implements IteratorAggregate
{
    /**
     * @param array<T> $items
     */
    public function __construct(
        private array $items
    ) {}

    /**
     * Filter items using a callback.
     *
     * @param callable(T): bool $callback
     * @return self<T>
     */
    public function filter(callable $callback): self
    {
        return new self(array_values(array_filter($this->items, $callback)));
    }

    /**
     * Map items using a callback.
     *
     * @template U
     * @param callable(T): U $callback
     * @return self<U>
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->items));
    }

    /**
     * Get the underlying array.
     *
     * @return array<T>
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get the underlying array (alias for all()).
     *
     * @return array<T>
     */
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * @return Traversable<int, T>
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}
