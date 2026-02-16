<?php

declare(strict_types=1);

/**
 * Dynamic Content Demo - Showcasing the real power of Elem.
 *
 * This example demonstrates why Elem is more than "HTML with extra steps":
 * - Variables: Pass data directly into your views
 * - Functions: Create reusable, parameterized components
 * - Loops: Map over collections to generate HTML
 * - Conditionals: Render content based on logic
 * - Composition: Build complex UIs from simple pieces
 *
 * Unlike templates (Blade, Twig), everything here is type-checked PHP.
 * Your IDE provides autocomplete, refactoring, and error detection.
 */

use Epic64\Elem\Element;
use function Epic64\Elem\{
    html, head, title, meta, stylesheet, body,
    div, h, p, span, a, el,
    ul, li, table, tr, th, td,
    list_of
};

// =============================================================================
// ENUMS
// =============================================================================
// Enums provide type safety and IDE autocomplete for component variants.
// No more typos like 'sucess' or 'waning' - the compiler catches them!

enum BadgeVariant: string
{
    case Default = 'default';
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';
    case Accent = 'accent';
    case Muted = 'muted';
}

enum AlertType: string
{
    case Info = 'info';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';

    public function icon(): string
    {
        return match ($this) {
            self::Info => 'ℹ️',
            self::Success => '✅',
            self::Warning => '⚠️',
            self::Error => '❌',
        };
    }
}

enum Trend: string
{
    case Up = 'up';
    case Down = 'down';
    case Neutral = 'neutral';

    public function icon(): string
    {
        return match ($this) {
            self::Up => '↑',
            self::Down => '↓',
            self::Neutral => '→',
        };
    }
}

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    public function badge(): BadgeVariant
    {
        return match ($this) {
            self::Admin => BadgeVariant::Primary,
            self::Editor => BadgeVariant::Accent,
            self::Viewer => BadgeVariant::Default,
        };
    }
}

enum OrderStatus: string
{
    case Processing = 'Processing';
    case Shipped = 'Shipped';
    case Delivered = 'Delivered';
    case Cancelled = 'Cancelled';

    public function badge(): BadgeVariant
    {
        return match ($this) {
            self::Processing => BadgeVariant::Warning,
            self::Shipped => BadgeVariant::Accent,
            self::Delivered => BadgeVariant::Success,
            self::Cancelled => BadgeVariant::Error,
        };
    }
}

// =============================================================================
// COMPONENT FUNCTIONS
// =============================================================================
// Components are just functions. No special syntax, no magic.
// They receive typed parameters and return Elements.

/**
 * A badge component for displaying status, roles, or tags.
 */
function badge(string $text, BadgeVariant $variant = BadgeVariant::Default): Element
{
    return span(class: "badge badge-{$variant->value}", text: $text);
}

/**
 * An alert box for displaying messages.
 */
function alert(string $message, AlertType $type = AlertType::Info): Element
{
    return div(class: "alert alert-{$type->value}")(
        span(class: 'alert-icon', text: $type->icon()),
        span(class: 'alert-message', text: $message)
    );
}

/**
 * A card component with optional header and footer.
 */
function card(string $title, ?string $subtitle = null): Element
{
    return div(class: 'card')(
        div(class: 'card-header')(
            h(3, class: 'card-title', text: $title),
            $subtitle ? p(class: 'card-subtitle', text: $subtitle) : null
        )
    );
}

/**
 * A stat card for displaying metrics.
 */
function statCard(string $label, string|int $value, Trend $trend = Trend::Neutral): Element
{
    return div(class: "stat-card stat-{$trend->value}")(
        span(class: 'stat-value', text: (string) $value),
        span(class: 'stat-label', text: $label),
        span(class: 'stat-trend', text: $trend->icon())
    );
}

/**
 * A user avatar with initials fallback.
 */
function avatar(string $name, ?string $imageUrl = null): Element
{
    $initials = implode('', array_map(
        fn($word) => mb_substr($word, 0, 1),
        array_slice(explode(' ', $name), 0, 2)
    ));

    if ($imageUrl) {
        return div(class: 'avatar')(
            el('img')->attr('src', $imageUrl)->attr('alt', $name)
        );
    }

    return div(class: 'avatar avatar-initials', text: strtoupper($initials));
}

/**
 * A user row component showing avatar, name, role, and status.
 */
function userRow(array $user): Element
{
    /** @var UserRole $role */
    $role = $user['role'];

    return div(class: 'user-row')(
        avatar($user['name'], $user['avatar'] ?? null),
        div(class: 'user-info')(
            span(class: 'user-name', text: $user['name']),
            span(class: 'user-email', text: $user['email'])
        ),
        badge($role->value, $role->badge()),
        badge(
            $user['active'] ? 'Active' : 'Inactive',
            $user['active'] ? BadgeVariant::Success : BadgeVariant::Muted
        )
    );
}

/**
 * A product card for e-commerce style display.
 */
function productCard(array $product): Element
{
    $hasDiscount = isset($product['originalPrice']) && $product['originalPrice'] > $product['price'];

    return div(class: 'product-card')(
        div(class: 'product-image')(
            $product['featured'] ? badge('Featured', BadgeVariant::Accent) : null,
            el('img')->attr('src', $product['image'])->attr('alt', $product['name'])
        ),
        div(class: 'product-info')(
            h(4, text: $product['name']),
            p(class: 'product-description', text: $product['description']),
            div(class: 'product-pricing')(
                span(class: 'product-price', text: '$' . number_format($product['price'], 2)),
                $hasDiscount ? span(class: 'product-original-price', text: '$' . number_format($product['originalPrice'], 2)) : null,
                $hasDiscount ? badge('-' . round((1 - $product['price'] / $product['originalPrice']) * 100) . '%', BadgeVariant::Success) : null
            ),
            div(class: 'product-meta')(
                badge($product['category']),
                $product['inStock'] ? badge('In Stock', BadgeVariant::Success) : badge('Out of Stock', BadgeVariant::Error)
            )
        )
    );
}

/**
 * A navigation menu component.
 */
function navMenu(array $items, string $currentPath = '/'): Element
{
    return el('nav', class: 'nav-menu')(
        ul()(
            ...array_map(
                fn($item) => li(class: $item['path'] === $currentPath ? 'active' : '')(
                    a($item['path'], text: $item['label']),
                    isset($item['badge']) ? badge($item['badge'], BadgeVariant::Accent) : null
                ),
                $items
            )
        )
    );
}

/**
 * A data table component with headers and rows.
 */
function dataTable(array $headers, array $rows): Element
{
    return table(class: 'data-table')(
        el('thead')(
            tr()(
                ...array_map(fn($h) => th(text: $h), $headers)
            )
        ),
        el('tbody')(
            ...array_map(
                fn($row) => tr()(
                    ...array_map(fn($cell) => td(text: (string) $cell), $row)
                ),
                $rows
            )
        )
    );
}

// =============================================================================
// SAMPLE DATA
// =============================================================================
// In a real app, this would come from a database, API, or user input.

$currentUser = [
    'name' => 'Jane Developer',
    'role' => 'admin',
    'notifications' => 3,
];

$users = [
    ['name' => 'Alice Johnson', 'email' => 'alice@example.org', 'role' => UserRole::Admin, 'active' => true, 'avatar' => null],
    ['name' => 'Bob Smith', 'email' => 'bob@example.org', 'role' => UserRole::Editor, 'active' => true, 'avatar' => null],
    ['name' => 'Charlie Brown', 'email' => 'charlie@example.org', 'role' => UserRole::Viewer, 'active' => false, 'avatar' => null],
    ['name' => 'Diana Prince', 'email' => 'diana@example.org', 'role' => UserRole::Admin, 'active' => true, 'avatar' => null],
    ['name' => 'Eve Wilson', 'email' => 'eve@example.org', 'role' => UserRole::Editor, 'active' => true, 'avatar' => null],
];

$products = [
    [
        'name' => 'Mechanical Keyboard',
        'description' => 'Cherry MX switches, RGB backlight',
        'price' => 149.99,
        'originalPrice' => 199.99,
        'category' => 'Electronics',
        'inStock' => true,
        'featured' => true,
        'image' => 'https://picsum.photos/seed/keyboard/300/200',
    ],
    [
        'name' => 'Ergonomic Mouse',
        'description' => 'Wireless, vertical design',
        'price' => 79.99,
        'category' => 'Electronics',
        'inStock' => true,
        'featured' => false,
        'image' => 'https://picsum.photos/seed/mouse/300/200',
    ],
    [
        'name' => 'Standing Desk',
        'description' => 'Electric height adjustment',
        'price' => 499.99,
        'originalPrice' => 599.99,
        'category' => 'Furniture',
        'inStock' => false,
        'featured' => true,
        'image' => 'https://picsum.photos/seed/desk/300/200',
    ],
    [
        'name' => 'Monitor Light Bar',
        'description' => 'Reduces eye strain, auto-dimming',
        'price' => 89.99,
        'category' => 'Lighting',
        'inStock' => true,
        'featured' => false,
        'image' => 'https://picsum.photos/seed/lightbar/300/200',
    ],
];

$navItems = [
    ['path' => '/', 'label' => 'Dashboard'],
    ['path' => '/users', 'label' => 'Users', 'badge' => '5'],
    ['path' => '/products', 'label' => 'Products'],
    ['path' => '/orders', 'label' => 'Orders', 'badge' => 'New'],
    ['path' => '/settings', 'label' => 'Settings'],
];

$stats = [
    ['label' => 'Total Users', 'value' => count($users), 'trend' => Trend::Up],
    ['label' => 'Active Users', 'value' => count(array_filter($users, fn($u) => $u['active'])), 'trend' => Trend::Up],
    ['label' => 'Products', 'value' => count($products), 'trend' => Trend::Neutral],
    ['label' => 'In Stock', 'value' => count(array_filter($products, fn($p) => $p['inStock'])), 'trend' => Trend::Down],
];

$orderData = [
    ['#1001', 'Alice Johnson', 'Mechanical Keyboard', '$149.99', 'Shipped'],
    ['#1002', 'Bob Smith', 'Ergonomic Mouse', '$79.99', 'Processing'],
    ['#1003', 'Charlie Brown', 'Monitor Light Bar', '$89.99', 'Delivered'],
];

// =============================================================================
// PAGE RENDERING
// =============================================================================
// Now we compose everything together, using all the PHP features we love.

$isAdmin = $currentUser['role'] === 'admin';
$hasNotifications = $currentUser['notifications'] > 0;

return html(lang: 'en')(
    head()(
        title(text: 'Dynamic Content Demo - Elem Examples'),
        meta(charset: 'UTF-8'),
        meta(name: 'viewport', content: 'width=device-width, initial-scale=1.0'),
        stylesheet(href: '/css/dynamic-content-demo.css')
    ),
    body()(
        div(class: 'app-layout')(
            // Sidebar with navigation
            el('aside', class: 'sidebar')(
                div(class: 'sidebar-header')(
                    h(2, text: '🚀 Elem Demo'),
                    p(text: 'Dynamic Content')
                ),
                navMenu($navItems, '/'),
                div(class: 'sidebar-footer')(
                    avatar($currentUser['name']),
                    div(class: 'user-meta')(
                        span(class: 'user-name', text: $currentUser['name']),
                        badge($currentUser['role'], BadgeVariant::Primary)
                    )
                )
            ),

            // Main content area
            el('main', class: 'main-content')(
                // Header with conditional notification badge
                el('header', class: 'content-header')(
                    div()(
                        h(1, text: 'Dashboard'),
                        p(text: 'Welcome back, ' . explode(' ', $currentUser['name'])[0] . '!')
                    ),
                    $hasNotifications
                        ? badge($currentUser['notifications'] . ' new', BadgeVariant::Accent)
                        : null
                ),

                a('/', class: 'back-link', text: '← Back to Examples'),

                // Admin-only alert
                $isAdmin
                    ? alert('You have admin privileges. Handle with care!', AlertType::Warning)
                    : null,

                // Stats grid
                div(class: 'section')(
                    h(2, text: 'Overview'),
                    div(class: 'stats-grid')(
                        ...array_map(
                            fn($stat) => statCard($stat['label'], $stat['value'], $stat['trend']),
                            $stats
                        )
                    )
                ),

                // User management section
                div(class: 'section')(
                    card('User Management', 'Manage your team members')(
                        // Filter to show only active users
                        h(4, text: 'Active Team Members'),
                        div(class: 'user-list')(
                            list_of($users)
                                ->filter(fn($user) => $user['active'])
                                ->map(fn($user) => userRow($user))
                        ),

                        // Show inactive users count
                        p(class: 'text-muted')(
                            'Plus ',
                            span(class: 'font-bold', text: (string) count(array_filter($users, fn($u) => !$u['active']))),
                            ' inactive user(s)'
                        )
                    )
                ),

                // Products section with filtering
                div(class: 'section')(
                    card('Featured Products', 'Our top picks for you')(
                        div(class: 'products-grid')(
                            list_of($products)
                                ->filter(fn($product) => $product['featured'])
                                ->map(fn($product) => productCard($product))
                        )
                    )
                ),

                // All products
                div(class: 'section')(
                    card('All Products', 'Browse our catalog')(
                        div(class: 'products-grid')(
                            ...array_map(fn($product) => productCard($product), $products)
                        )
                    )
                ),

                // Data table section
                div(class: 'section')(
                    card('Recent Orders', 'Track your latest orders')(
                        dataTable(
                            ['Order ID', 'Customer', 'Product', 'Total', 'Status'],
                            $orderData
                        )
                    )
                ),

                // Conditional content based on data
                div(class: 'section')(
                    h(2, text: 'Conditional Rendering'),
                    div(class: 'conditional-examples')(
                        // Show different content based on user count
                        count($users) > 3
                            ? alert('You have a growing team! Consider upgrading your plan.', AlertType::Info)
                            : alert('Your team is small. Invite more members!', AlertType::Info),

                        // Show out-of-stock warning if any products are unavailable
                        count(array_filter($products, fn($p) => !$p['inStock'])) > 0
                            ? alert(count(array_filter($products, fn($p) => !$p['inStock'])) . ' product(s) are out of stock.', AlertType::Warning)
                            : alert('All products are in stock!', AlertType::Success),

                        // Admin-only content
                        $isAdmin ? div(class: 'admin-panel')(
                            h(4, text: '🔐 Admin Panel'),
                            p(text: 'This section is only visible to administrators.'),
                            ul()(
                                li(text: 'Manage user permissions'),
                                li(text: 'View system logs'),
                                li(text: 'Configure integrations')
                            )
                        ) : null
                    )
                ),

                // Code showcase
                div(class: 'section')(
                    card('How It Works', 'The code behind this page')(
                        el('pre', class: 'code-block')(
                            el('code', text: <<<'PHP'
// Components are just functions
function badge(string $text, string $variant = 'default'): Element
{
    return span(class: "badge badge-$variant", text: $text);
}

// Use them with your data
$users = getUsersFromDatabase();

div(class: 'user-list')(
    list_of($users)
        ->filter(fn($user) => $user['active'])
        ->map(fn($user) => userRow($user))
)

// Conditional rendering is just PHP
$isAdmin ? alert('Admin mode', 'warning') : null
PHP
                            )
                        )
                    )
                )
            )
        )
    )
);
