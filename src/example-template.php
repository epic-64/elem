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

echo html(lang: 'en')(
    head()(
        meta(charset: 'UTF-8'),
        meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
        title('Vibe HTML Template Example'),
        style(<<<CSS
            .container { max-width: 800px; margin: 0 auto; padding: 20px; }
            .card { border: 1px solid #ccc; border-radius: 8px; padding: 16px; margin: 16px 0; }
            .btn { padding: 8px 16px; border-radius: 4px; cursor: pointer; }
            .btn-primary { background: #007bff; color: white; border: none; }
            .nav { list-style: none; display: flex; gap: 16px; padding: 0; }
            .form-group { margin-bottom: 12px; }
            .form-control { width: 100%; padding: 8px; box-sizing: border-box; }
            CSS
        )
    ),
    body()(
        div(class: 'container')(
            h(1, text: 'Welcome to Vibe HTML'),

            p(text: 'This demonstrates embedding the functional API directly in HTML templates.'),

            card(title: 'Navigation')(
                ul(class: 'nav')(
                    li()(a('/', text: 'Home')),
                    li()(a('/about', text: 'About')),
                    li()(a('/contact', text: 'Contact'))
                )
            ),

            card('Login Form')(
                form(id: 'login-form', action: '/login')(
                    formGroup(labelText: 'Email:', inputId: 'email')(
                        input(type: 'email', id: 'email', class: 'form-control')
                            ->required()
                            ->placeholder('you@example.org')
                            ->attr('pattern', '[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$')
                            ->script(<<<JS
                                el.addEventListener('input', () => {
                                    this.style.borderColor = this.checkValidity() ? '#28a745' : '#dc3545';
                                });
                            JS)
                    ),
                    formGroup(labelText: 'Password:', inputId: 'password')(
                        input(type: 'password', id: 'password', class: 'form-control')
                            ->attr('required', 'required')
                            ->attr('minlength', '8')
                            ->script(<<<JS
                                el.addEventListener('input', () => {
                                    this.style.borderColor = this.checkValidity() ? '#28a745' : '#dc3545';
                                });
                            JS)
                    ),
                    button(id: 'submit', class: 'btn btn-primary', text: 'Login', type: 'submit')
                )
            )
        )
    )
);
