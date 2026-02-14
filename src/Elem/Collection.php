<?php

declare(strict_types=1);

namespace Epic64\Elem;

/**
 * A simple collection class for fluent array transformations.
 *
 * @template T
 */
class Collection
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
     * Make collection invokable, returning the underlying array.
     * This allows collections to be used directly as element children.
     *
     * @return array<T>
     */
    public function __invoke(): array
    {
        return $this->items;
    }
}
