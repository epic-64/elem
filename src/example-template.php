<?php
require_once __DIR__ . '/vibe-html.php';
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
        h(1, 'Welcome to Vibe HTML'),

        p('This demonstrates embedding the functional API directly in HTML templates.'),

        div(class: 'card')(
            h(2, 'Navigation'),
            ul(class: 'nav')(
                li()(a('/', 'Home')->blank()),
                li()(a('/about', 'About')),
                li()(a('/contact', 'Contact'))
            )
        ),

        div(class: 'card')(
            h(2, 'Login Form'),
            form('/login')(
                div(class: 'form-group')(
                    label('Email:', 'email'),
                    input('email', id: 'email', class: 'form-control')
                ),
                div(class: 'form-group')(
                    label(text: 'Password:', for: 'password'),
                    input(type: 'password', id: 'password', class: 'form-control')->required()
                ),
                button(id: 'submit', class: 'btn btn-primary', text: 'Login')
            )
        )
    ) ?>
</body>
</html>
