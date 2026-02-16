# Templating & Layouts

Build reusable page layouts with multiple "slots" for injecting content into different areas (head, sidebar, main, footer). This eliminates boilerplate while keeping full flexibility.

## Base Page Layout

Start with a function that handles the HTML boilerplate:

```php
use Epic64\Elem\Element;
use function Epic64\Elem\{html, head, title, meta, body};

/**
 * @param string $pageTitle
 * @param list<Element> $headSlot Additional elements for <head>
 * @param list<Element> $bodySlot Main body content
 */
function pageLayout(
    string $pageTitle,
    array $headSlot = [],
    array $bodySlot = [],
): Element {
    return html(lang: 'en')(
        head()(
            meta(charset: 'UTF-8'),
            meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
            title(text: $pageTitle),
            ...$headSlot,
        ),
        body()(
            ...$bodySlot,
        )
    );
}
```

## Multi-Slot Dashboard Layout

Build on the base layout to create more complex templates:

```php
use function Epic64\Elem\{div, el, stylesheet};

/**
 * @param string $pageTitle
 * @param list<Element> $headerSlot Header content (logo, nav, user menu)
 * @param list<Element> $sidebarSlot Sidebar navigation
 * @param list<Element> $mainSlot Main content area
 * @param list<Element> $footerSlot Footer content
 */
function dashboardLayout(
    string $pageTitle,
    array $headerSlot = [],
    array $sidebarSlot = [],
    array $mainSlot = [],
    array $footerSlot = [],
): Element {
    return pageLayout(
        pageTitle: $pageTitle,
        headSlot: [
            stylesheet('/css/dashboard.css'),
        ],
        bodySlot: [
            div(class: 'dashboard-layout')(
                el('header', class: 'dashboard-header')(...$headerSlot),
                el('aside', class: 'dashboard-sidebar')(...$sidebarSlot),
                el('main', class: 'dashboard-main')(...$mainSlot),
                el('footer', class: 'dashboard-footer')(...$footerSlot),
            ),
        ],
    );
}
```

## Reusable Component Layouts

The same pattern works for smaller components like cards and modals:

```php
use function Epic64\Elem\{div, h};

/**
 * @param string $cardTitle
 * @param list<Element> $headerSlot Badges, action buttons
 * @param list<Element> $bodySlot Main card content
 * @param list<Element> $footerSlot Footer actions
 */
function cardLayout(
    string $cardTitle,
    array $headerSlot = [],
    array $bodySlot = [],
    array $footerSlot = [],
): Element {
    return div(class: 'card')(
        div(class: 'card-header')(
            h(3, class: 'card-title', text: $cardTitle),
            count($headerSlot) > 0 ? div(class: 'card-actions')(...$headerSlot) : null,
        ),
        div(class: 'card-body')(...$bodySlot),
        count($footerSlot) > 0 ? div(class: 'card-footer')(...$footerSlot) : null,
    );
}

/**
 * @param string $modalTitle
 * @param string $id Modal ID for targeting
 * @param list<Element> $bodySlot Modal content
 * @param list<Element> $footerSlot Footer actions (confirm, cancel buttons)
 */
function modalLayout(
    string $modalTitle,
    string $id,
    array $bodySlot = [],
    array $footerSlot = [],
): Element {
    return div(id: $id, class: 'modal')(
        div(class: 'modal-backdrop'),
        div(class: 'modal-dialog')(
            div(class: 'modal-header')(
                h(4, class: 'modal-title', text: $modalTitle),
                el('button', class: 'modal-close', text: '×')
                    ->attr('aria-label', 'Close'),
            ),
            div(class: 'modal-body')(...$bodySlot),
            count($footerSlot) > 0 
                ? div(class: 'modal-footer')(...$footerSlot) 
                : null,
        ),
    );
}
```

## Using Layouts

Fill only the slots you need - empty slots render nothing:

```php
return dashboardLayout(
    pageTitle: 'My Dashboard',

    headerSlot: [
        h(1, text: '🚀 My App'),
        badge('Admin', BadgeVariant::Primary),
    ],

    sidebarSlot: [
        navMenu($menuItems),
    ],

    mainSlot: [
        // Nest card layouts inside the dashboard
        cardLayout(
            cardTitle: 'User Statistics',
            headerSlot: [badge('Live', BadgeVariant::Success)],
            bodySlot: [
                statCard(new Stat('Total Users', 1234, Trend::Up)),
                statCard(new Stat('Active Today', 892, Trend::Up)),
            ],
            footerSlot: [
                a('/users', text: 'View all users →'),
            ],
        ),

        cardLayout(
            cardTitle: 'Recent Activity',
            bodySlot: [activityFeed($recentActivity)],
        ),

        // Modal for confirmations
        modalLayout(
            modalTitle: 'Confirm Deletion',
            id: 'delete-modal',
            bodySlot: [
                p(text: 'Are you sure? This action cannot be undone.'),
            ],
            footerSlot: [
                button('Cancel', ButtonVariant::Ghost),
                button('Delete', ButtonVariant::Danger),
            ],
        ),
    ],

    footerSlot: [
        p(text: '© 2024 My App'),
    ],
);
```

## Benefits

- **Type-safe slots**: PHPDoc `@param list<Element>` ensures you pass valid content
- **No inheritance complexity**: Just function composition
- **Flexible nesting**: Layouts can contain other layouts
- **Conditional slots**: Use `count($slot) > 0` to skip empty wrappers
- **IDE support**: Full autocomplete and refactoring
- **No magic**: It's just PHP functions calling other functions
