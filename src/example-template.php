<?php
require_once __DIR__ . '/vibe-html.php';

// Define reusable components as simple functions
function card(string $title, Element|string ...$children): Element
{
    return div(class: 'card')(
        h(2, text: $title),
        ...$children
    );
}

function navItem(string $href, string $text, bool $blank = false): Element
{
    return li()(
        $blank ? a($href, text: $text)->blank() : a($href, text: $text)
    );
}

function formGroup(string $labelText, string $inputId, Element|string ...$children): Element
{
    return div(class: 'form-group')(
        label(text: $labelText, for: $inputId),
        ...$children
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vibe HTML Template Example</title>
    <style>
        .container { max-width: 800px; margin: 0 auto; padding: 20px; }
        .card { border: 1px solid #ccc; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .btn { padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; border: none; }
        .nav { list-style: none; display: flex; gap: 16px; padding: 0; }
        .form-group { margin-bottom: 12px; }
        .form-control { width: 100%; padding: 8px; box-sizing: border-box; }
    </style>
</head>
<body>
    <?= div(class: 'container')(
        h(1, text: 'Welcome to Vibe HTML'),

        p(text: 'This demonstrates embedding the functional API directly in HTML templates.'),

        card('Navigation',
            ul(class: 'nav')(
                navItem('/', 'Home', blank: true),
                navItem('/about', 'About'),
                navItem('/contact', 'Contact')
            )
        ),

        card('Login Form',
            form(action: '/login')(
                formGroup('Email:', 'email',
                    input(type: 'email', id: 'email', class: 'form-control')
                ),
                formGroup('Password:', 'password',
                    input(type: 'password', id: 'password', class: 'form-control')->required()
                ),
                button(id: 'submit', class: 'btn btn-primary', text: 'Login')
            )
        )
    ) ?>
</body>
</html>
