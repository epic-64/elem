<?php
require_once __DIR__ . '/vibe-html.php';

// Define reusable components as simple functions
function card(string $title): Element
{
    return div(class: 'card')(
        h(2, text: $title),
    );
}

function formGroup(string $labelText, string $inputId): Element
{
    return div(class: 'form-group')(
        label(text: $labelText, for: $inputId),
    );
}

// create a collection on which we can call Map
readonly class Mapping {
    /** @param array $items */
    public function __construct(public array $items) {}

    /**
     * @param callable(mixed): Element $callback
     * @return array<Element>
     */
    public function map_element(callable $callback): array {
        return array_map($callback, $this->items);
    }
}

// helper function for creating a mapping collection
function data(array $items): Mapping {
    return new Mapping($items);
}

$users = [
    ['name' => 'Alice', 'email' => 'alice@example.org'],
    ['name' => 'Bob', 'email' => 'bob@example.org'],
    ['name' => 'Charlie', 'email' => 'charlie@example.org'],
];

echo html(lang: 'en')(
    head()(
        title(text: 'Vibe HTML Template Example'),
        meta(charset: 'UTF-8'),
        meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
        script()->attr('src', 'https://unpkg.com/htmx.org@2.0.4'),
        script()->attr('defer', 'defer')->attr('src', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js'),
        style(<<<CSS
            .container { max-width: 800px; margin: 0 auto; padding: 20px; }
            .card { border: 1px solid #ccc; border-radius: 8px; padding: 16px; margin: 16px 0; }
            .btn { padding: 8px 16px; border-radius: 4px; cursor: pointer; }
            .btn-primary { background: #007bff; color: white; border: none; }
            .nav { list-style: none; display: flex; gap: 16px; padding: 0; }
            .form-group { margin-bottom: 12px; }
            .form-control { width: 100%; padding: 8px; box-sizing: border-box; }
        CSS)
    ),
    body()(
        div(class: 'container')(
            h(1, text: 'Welcome to Vibe HTML'),

            p(text: 'This demonstrates embedding the functional API directly in HTML templates.'),

            data($users)->map_element(fn($user) => div(class: 'card')(
                h(3, text: $user['name']),
                p(text: $user['email'])
            )),

            card(title: 'Navigation')(
                ul(class: 'nav')(
                    li()(a('/', text: 'Home')),
                    li()(a('/about', text: 'About')),
                    li()(a('/contact', text: 'Contact'))
                )
            ),

            card('Login Form')(
                form(id: 'login-form', action: '/login')
                    ->attr('hx-post', '/login')
                    ->attr('hx-target', '#login-form')
                    ->attr('hx-swap', 'outerHTML')(
                    formGroup(labelText: 'Email:', inputId: 'email')(
                        input(type: 'email', id: 'email', class: 'form-control')
                            ->required()
                            ->placeholder('you@example.org')
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
