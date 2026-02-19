<?php

declare(strict_types=1);

/**
 * Layout Demo - Complex templates with multiple slots.
 *
 * This example demonstrates how to build reusable page layouts where users
 * can inject content into multiple "slots" (head, sidebar, main, footer, etc.)
 *
 * Key patterns:
 * - Layout functions that encapsulate the HTML boilerplate
 * - Multiple slots for different content areas
 * - Type-safe slot content using typed parameters
 * - Nested layouts (e.g., a card layout within a page layout)
 */

use Epic64\Elem\Element;
use function Epic64\Elem\{
    html, head, title, meta, stylesheet, script, body,
    div, h, p, span, a, el,
    ul, li
};

// =============================================================================
// LAYOUT FUNCTIONS
// =============================================================================

/**
 * A base HTML page layout with slots for head content, body content, and scripts.
 *
 * This eliminates the DOCTYPE, viewport, charset boilerplate from every page.
 *
 * @param string $pageTitle The page title
 * @param list<Element> $headSlot Additional elements for <head> (stylesheets, meta tags)
 * @param list<Element> $bodySlot The main body content
 * @param list<Element> $scriptsSlot Scripts to load at end of body
 */
function pageLayout(
    string $pageTitle,
    array $headSlot = [],
    array $bodySlot = [],
    array $scriptsSlot = [],
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
            ...$scriptsSlot,
        )
    );
}

/**
 * A dashboard layout with header, sidebar, main content, and footer slots.
 *
 * @param string $pageTitle The page title
 * @param list<Element> $headerSlot Header content (logo, nav, user menu)
 * @param list<Element> $sidebarSlot Sidebar navigation
 * @param list<Element> $mainSlot Main content area
 * @param list<Element> $footerSlot Footer content
 * @param list<Element> $headSlot Additional head elements
 */
function dashboardLayout(
    string $pageTitle,
    array $headerSlot = [],
    array $sidebarSlot = [],
    array $mainSlot = [],
    array $footerSlot = [],
    array $headSlot = [],
): Element {
    return pageLayout(
        pageTitle: $pageTitle,
        headSlot: [
            stylesheet('/css/layout-demo.css'),
            ...$headSlot,
        ],
        bodySlot: [
            div(class: 'dashboard-layout')(
                el('header', class: 'dashboard-header')(
                    ...$headerSlot,
                ),
                el('aside', class: 'dashboard-sidebar')(
                    ...$sidebarSlot,
                ),
                el('main', class: 'dashboard-main')(
                    ...$mainSlot,
                ),
                el('footer', class: 'dashboard-footer')(
                    ...$footerSlot,
                ),
            ),
        ],
    );
}

/**
 * A card component with header, body, and footer slots.
 *
 * @param string $cardTitle Card title
 * @param list<Element> $headerSlot Additional header content (badges, actions)
 * @param list<Element> $bodySlot Main card content
 * @param list<Element> $footerSlot Footer actions (buttons, links)
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
            count($headerSlot) > 0
                ? div(class: 'card-header-actions')(...$headerSlot)
                : null,
        ),
        div(class: 'card-body')(
            ...$bodySlot,
        ),
        count($footerSlot) > 0
            ? div(class: 'card-footer')(...$footerSlot)
            : null,
    );
}

/**
 * A modal dialog with header, body, and footer slots.
 *
 * @param string $modalTitle Modal title
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
            div(class: 'modal-body')(
                ...$bodySlot,
            ),
            count($footerSlot) > 0
                ? div(class: 'modal-footer')(...$footerSlot)
                : null,
        ),
    );
}

// =============================================================================
// HELPER COMPONENTS
// =============================================================================

enum BadgeVariant: string
{
    case Default = 'default';
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';
}

enum ButtonVariant: string
{
    case Primary = 'primary';
    case Secondary = 'secondary';
    case Danger = 'danger';
    case Ghost = 'ghost';
}

function badge(string $text, BadgeVariant $variant = BadgeVariant::Default): Element
{
    return span(class: "badge badge-{$variant->value}", text: $text);
}

function button(string $text, ButtonVariant $variant = ButtonVariant::Primary): Element
{
    return el('button', class: "btn btn-{$variant->value}", text: $text);
}

function navItem(string $label, string $href, bool $active = false, ?string $badge = null): Element
{
    return li(class: $active ? 'nav-item active' : 'nav-item')(
        a($href, class: 'nav-link')(
            span(text: $label),
            $badge !== null ? span(class: 'nav-badge', text: $badge) : null,
        )
    );
}

// =============================================================================
// PAGE CONTENT
// =============================================================================

// Build the page using the dashboardLayout function
$page = dashboardLayout(
    pageTitle: 'Layout Demo - Elem Examples',

    headerSlot: [
        div(class: 'header-brand')(
            h(1, text: '🧩 Elem Layouts'),
        ),
        el('nav', class: 'header-nav')(
            a('/', class: 'header-link', text: '← Back to Examples'),
        ),
        div(class: 'header-user')(
            span(class: 'user-name', text: 'Jane Developer'),
            badge('Admin', BadgeVariant::Primary),
        ),
    ],

    sidebarSlot: [
        el('nav', class: 'sidebar-nav')(
            h(4, class: 'nav-heading', text: 'Navigation'),
            ul(class: 'nav-list')(
                navItem('Dashboard', '/dashboard', active: true),
                navItem('Users', '/users', badge: '5'),
                navItem('Products', '/products'),
                navItem('Orders', '/orders', badge: 'New'),
                navItem('Analytics', '/analytics'),
            ),
            h(4, class: 'nav-heading', text: 'Settings'),
            ul(class: 'nav-list')(
                navItem('Profile', '/profile'),
                navItem('Preferences', '/preferences'),
                navItem('Security', '/security'),
            ),
        ),
    ],

    mainSlot: [
        // Section intro
        div(class: 'section')(
            h(2, text: 'Multi-Slot Layouts'),
            p(class: 'lead', text: 'This page demonstrates how to build complex layouts with multiple content slots using Elem.'),
        ),

        // Cards section
        div(class: 'section')(
            h(3, text: 'Card Layouts'),
            div(class: 'card-grid')(
                // Card with all slots
                cardLayout(
                    cardTitle: 'User Statistics',
                    headerSlot: [
                        badge('Live', BadgeVariant::Success),
                    ],
                    bodySlot: [
                        div(class: 'stat-row')(
                            div(class: 'stat')(
                                span(class: 'stat-value', text: '1,234'),
                                span(class: 'stat-label', text: 'Total Users'),
                            ),
                            div(class: 'stat')(
                                span(class: 'stat-value', text: '892'),
                                span(class: 'stat-label', text: 'Active Today'),
                            ),
                        ),
                    ],
                    footerSlot: [
                        a('/users', class: 'card-link', text: 'View all users →'),
                    ],
                ),

                // Card with minimal slots
                cardLayout(
                    cardTitle: 'Quick Actions',
                    bodySlot: [
                        div(class: 'action-buttons')(
                            button('Create User', ButtonVariant::Primary),
                            button('Import Data', ButtonVariant::Secondary),
                            button('Export CSV', ButtonVariant::Ghost),
                        ),
                    ],
                ),

                // Card with rich content
                cardLayout(
                    cardTitle: 'Recent Activity',
                    headerSlot: [
                        badge('3 new', BadgeVariant::Warning),
                    ],
                    bodySlot: [
                        ul(class: 'activity-list')(
                            li(class: 'activity-item')(
                                span(class: 'activity-icon', text: '👤'),
                                span(class: 'activity-text', text: 'New user registered'),
                                span(class: 'activity-time', text: '2 min ago'),
                            ),
                            li(class: 'activity-item')(
                                span(class: 'activity-icon', text: '📦'),
                                span(class: 'activity-text', text: 'Order #1234 shipped'),
                                span(class: 'activity-time', text: '15 min ago'),
                            ),
                            li(class: 'activity-item')(
                                span(class: 'activity-icon', text: '💬'),
                                span(class: 'activity-text', text: 'New comment on post'),
                                span(class: 'activity-time', text: '1 hour ago'),
                            ),
                        ),
                    ],
                    footerSlot: [
                        a('/activity', class: 'card-link', text: 'View all activity →'),
                    ],
                ),
            ),
        ),

        // Modal example
        div(class: 'section')(
            h(3, text: 'Modal Layout'),
            p(text: 'Modals also use the slot pattern for flexible content:'),
            div(class: 'modal-preview')(
                modalLayout(
                    modalTitle: 'Confirm Deletion',
                    id: 'delete-modal',
                    bodySlot: [
                        p(text: 'Are you sure you want to delete this item? This action cannot be undone.'),
                        div(class: 'modal-warning')(
                            span(class: 'warning-icon', text: '⚠️'),
                            span(text: 'This will permanently remove all associated data.'),
                        ),
                    ],
                    footerSlot: [
                        button('Cancel', ButtonVariant::Ghost),
                        button('Delete', ButtonVariant::Danger),
                    ],
                ),
            ),
        ),

        // Code example
        div(class: 'section')(
            h(3, text: 'How It Works'),
            el('pre', class: 'code-block')(
                el('code', text: <<<'PHP'
                    // Define a layout function with typed slot parameters
                    /**
                     * @param string $pageTitle
                     * @param list<Element> $headerSlot
                     * @param list<Element> $sidebarSlot
                     * @param list<Element> $mainSlot
                     * @param list<Element> $footerSlot
                     */
                    function dashboardLayout(
                        string $pageTitle,
                        array $headerSlot = [],
                        array $sidebarSlot = [],
                        array $mainSlot = [],
                        array $footerSlot = [],
                    ): Element {
                        return html(lang: 'en')(
                            head()(...),
                            body()(
                                el('header')(...$headerSlot),
                                el('aside')(...$sidebarSlot),
                                el('main')(...$mainSlot),
                                el('footer')(...$footerSlot),
                            )
                        );
                    }
                    
                    // Use it - fill only the slots you need
                    return dashboardLayout(
                        pageTitle: 'My Dashboard',
                        headerSlot: [
                            h(1, text: 'Welcome'),
                            navMenu($items),
                        ],
                        mainSlot: [
                            userCard($currentUser),
                            statsGrid($stats),
                        ],
                    );
                    PHP
                ),
            ),
        ),
    ],

    footerSlot: [
        p(text: '© 2024 Elem Examples. Built with type-safe PHP.'),
        div(class: 'footer-links')(
            a('https://github.com/epic-64/elem', text: 'GitHub'),
            span(class: 'separator', text: '•'),
            a('/docs', text: 'Documentation'),
            span(class: 'separator', text: '•'),
            a('/examples', text: 'Examples'),
        ),
    ],
);

return $page->toHtml(pretty: false);