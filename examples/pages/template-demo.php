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
    html, head, title, meta, script, style, body,
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

// CSS styles
$styles = <<<CSS
    * { margin: 0; padding: 0; box-sizing: border-box; }
    
    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
        background: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 50%, #16213e 100%);
        min-height: 100vh;
        color: #e4e4e7;
        line-height: 1.6;
    }
    
    .container {
        max-width: 900px;
        margin: 0 auto;
        padding: 60px 24px;
    }
    
    .back-link {
        display: inline-block;
        color: #a5b4fc;
        text-decoration: none;
        margin-bottom: 24px;
        font-size: 0.9rem;
    }
    
    .back-link:hover {
        text-decoration: underline;
    }
    
    h1 {
        font-size: 3rem;
        font-weight: 700;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 16px;
        letter-spacing: -0.02em;
    }
    
    h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: #a5b4fc;
        margin-bottom: 20px;
        letter-spacing: -0.01em;
    }
    
    h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #f1f5f9;
        margin-bottom: 4px;
    }
    
    p {
        color: #94a3b8;
        margin-bottom: 8px;
    }
    
    .card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 16px;
        padding: 28px;
        margin: 24px 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
    }
    
    .card:hover {
        border-color: rgba(139, 92, 246, 0.3);
        box-shadow: 0 8px 40px rgba(139, 92, 246, 0.15);
        transform: translateY(-2px);
    }
    
    .user-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin: 32px 0;
    }
    
    .user-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 12px;
        padding: 20px;
        transition: all 0.2s ease;
    }
    
    .user-card:hover {
        background: rgba(139, 92, 246, 0.08);
        border-color: rgba(139, 92, 246, 0.2);
    }
    
    .user-card p {
        font-size: 0.875rem;
        color: #64748b;
    }
    
    .nav {
        list-style: none;
        display: flex;
        gap: 8px;
        padding: 0;
        flex-wrap: wrap;
    }
    
    .nav li a {
        display: inline-block;
        padding: 10px 20px;
        color: #a5b4fc;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        border-radius: 8px;
        background: rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.2);
        transition: all 0.2s ease;
    }
    
    .nav li a:hover {
        background: rgba(139, 92, 246, 0.2);
        border-color: rgba(139, 92, 246, 0.4);
        color: #c4b5fd;
        transform: translateY(-1px);
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        font-size: 0.875rem;
        font-weight: 500;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    
    .form-control {
        width: 100%;
        padding: 14px 16px;
        font-size: 1rem;
        color: #e4e4e7;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        outline: none;
        transition: all 0.2s ease;
    }
    
    .form-control::placeholder {
        color: #64748b;
    }
    
    .form-control:focus {
        border-color: rgba(139, 92, 246, 0.5);
        box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
    }
    
    .btn {
        padding: 14px 28px;
        font-size: 0.95rem;
        font-weight: 600;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(102, 126, 234, 0.4);
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    .intro-text {
        font-size: 1.125rem;
        color: #94a3b8;
        margin-bottom: 40px;
        max-width: 600px;
    }
CSS;

// Build and return the page
return html(lang: 'en')(
    head()(
        title(text: 'Template Demo - Elem Examples'),
        meta(charset: 'UTF-8'),
        meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
        script(src: 'https://unpkg.com/htmx.org@2.0.4'),
        script(src: 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js')->attr('defer', 'defer'),
        style($styles)
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
