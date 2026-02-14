<?php

declare(strict_types=1);

namespace Epic64\Elem;

use DOMElement;

/**
 * Manages DOM document scopes for element creation.
 * Provides a shared default scope for efficiency, with the ability
 * to create isolated scopes when needed (e.g., comparing documents).
 */
class ElementFactory
{
    private static ?HtmlDocument $sharedScope = null;
    private static ?HtmlDocument $currentScope = null;

    /**
     * Get the current active scope (either explicit or shared default).
     */
    public static function getScope(): HtmlDocument
    {
        if (self::$currentScope !== null) {
            return self::$currentScope;
        }

        if (self::$sharedScope === null) {
            self::$sharedScope = new HtmlDocument();
        }
        return self::$sharedScope;
    }

    /**
     * Create a new isolated scope and set it as active.
     * Returns the new scope for use in a scoped context.
     */
    public static function createScope(): HtmlDocument
    {
        $scope = new HtmlDocument();
        self::$currentScope = $scope;
        return $scope;
    }

    /**
     * Set the active scope (or null to use shared default).
     */
    public static function setScope(?HtmlDocument $scope): void
    {
        self::$currentScope = $scope;
    }

    /**
     * Execute a callback within an isolated scope.
     * Automatically restores the previous scope afterward.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public static function withScope(callable $callback): mixed
    {
        $previousScope = self::$currentScope;
        self::$currentScope = new HtmlDocument();
        try {
            return $callback();
        } finally {
            self::$currentScope = $previousScope;
        }
    }

    public static function createElement(string $tagName, ?string $text = null): DOMElement
    {
        return self::getScope()->createElement($tagName, $text);
    }

    /**
     * Reset the shared scope (useful for testing).
     */
    public static function reset(): void
    {
        self::$sharedScope = null;
        self::$currentScope = null;
    }
}
