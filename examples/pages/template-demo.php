<?php

declare(strict_types=1);

/**
 * Template Demo - A comprehensive example showing various Elem features.
 *
 * Features demonstrated:
 * - Reusable components as functions
 * - Data mapping with array_map
 * - HTMX integration
 * - Alpine.js integration
 * - Form handling
 * - CSS styling
 */

use Epic64\Elem\Element;
use function Epic64\Elem\{
    html, head, title, meta, script, stylesheet, body,
    div, h, p, span, a,
    ul, li,
    form, label, input, button
};

// Define reusable components as simple functions

/**
 * Create a styled card component.
 */
function card(string $title): Element
{
    return div(class: 'card')(
        h(2, text: $title),
    );
}

/**
 * Create a form group with label.
 */
function formGroup(string $labelText, string $inputId): Element
{
    return div(class: 'form-group')(
        label(text: $labelText, for: $inputId),
    );
}

// Sample user data
$users = [
    ['name' => 'Alice', 'email' => 'alice@example.org'],
    ['name' => 'Bob', 'email' => 'bob@example.org'],
    ['name' => 'Charlie', 'email' => 'charlie@example.org'],
];


// Build and return the page
return html(lang: 'en')(
    head()(
        title(text: 'Template Demo - Elem Examples'),
        meta(charset: 'UTF-8'),
        meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
        stylesheet(href: '/css/template-demo.css'),
        script(src: 'https://unpkg.com/htmx.org@2.0.4'),
        script(src: 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js')->attr('defer', 'defer')
    ),
    body()(
        div(class: 'container')(
            a('/', class: 'back-link', text: '← Back to Examples'),

            h(1, text: 'Welcome to Elem'),

            p(class: 'intro-text', text: 'This demonstrates embedding the functional API directly in HTML templates.'),

            // User grid - using array_map for data binding
            div(class: 'user-grid')(
                ...array_map(
                    fn($user) => div(class: 'user-card')(
                        h(3, text: $user['name']),
                        p(text: $user['email'])
                    ),
                    $users
                ),
            ),

            // Navigation card using component
            card(title: 'Navigation')(
                ul(class: 'nav')(
                    li()(a('/', text: 'Home')),
                    li()(a('/about', text: 'About')),
                    li()(a('/contact', text: 'Contact'))
                )
            ),

            // Login form with HTMX and Alpine.js
            card('Login Form')(
                form(id: 'login-form', action: '/login')
                    ->attr('hx-post', '/login')
                    ->attr('hx-target', '#login-form')
                    ->attr('hx-swap', 'outerHTML')(
                    formGroup(labelText: 'Email:', inputId: 'email')(
                        input(type: 'email', id: 'email', class: 'form-control')
                            ->attr('required', 'required')
                            ->attr('placeholder', 'you@example.org')
                            ->attr('pattern', '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$')
                            ->attr('x-data', '{ valid: null }')
                            ->attr('x-on:input', 'valid = $el.checkValidity()')
                            ->attr(':style', "valid === null ? '' : (valid ? 'border-color: #28a745' : 'border-color: #dc3545')")
                    ),
                    formGroup(labelText: 'Password:', inputId: 'password')(
                        input(type: 'password', id: 'password', class: 'form-control')
                            ->attr('required', 'required')
                            ->attr('minlength', '8')
                            ->attr('x-data', '{ valid: null }')
                            ->attr('x-on:input', 'valid = $el.checkValidity()')
                            ->attr(':style', "valid === null ? '' : (valid ? 'border-color: #28a745' : 'border-color: #dc3545')")
                    ),
                    button(id: 'submit', class: 'btn btn-primary', text: 'Login', type: 'submit')
                )
            ),
        )
    )
);
