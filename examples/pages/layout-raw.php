<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Layout Demo - Elem Examples
    </title>
    <link href="/css/layout-demo.css" rel="stylesheet">
</head>
<body>
<div class="dashboard-layout">
    <header class="dashboard-header">
        <div class="header-brand">
            <h1>
                🧩 Elem Layouts
            </h1>
        </div>
        <nav class="header-nav">
            <a href="/" class="header-link">
                ← Back to Examples
            </a>
        </nav>
        <div class="header-user">
          <span class="user-name">
            Jane Developer
          </span>
            <span class="badge badge-primary">
            Admin
          </span>
        </div>
    </header>
    <aside class="dashboard-sidebar">
        <nav class="sidebar-nav">
            <h4 class="nav-heading">
                Navigation
            </h4>
            <ul class="nav-list">
                <li class="nav-item active">
                    <a href="/dashboard" class="nav-link">
                <span>
                  Dashboard
                </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/users" class="nav-link">
                <span>
                  Users
                </span>
                        <span class="nav-badge">
                  5
                </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/products" class="nav-link">
                <span>
                  Products
                </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/orders" class="nav-link">
                <span>
                  Orders
                </span>
                        <span class="nav-badge">
                  New
                </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/analytics" class="nav-link">
                <span>
                  Analytics
                </span>
                    </a>
                </li>
            </ul>
            <h4 class="nav-heading">
                Settings
            </h4>
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="/profile" class="nav-link">
                <span>
                  Profile
                </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/preferences" class="nav-link">
                <span>
                  Preferences
                </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/security" class="nav-link">
                <span>
                  Security
                </span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <main class="dashboard-main">
        <div class="section">
            <h2>
                Multi-Slot Layouts
            </h2>
            <p class="lead">
                This page demonstrates how to build complex layouts with multiple content slots using Elem.
            </p>
        </div>
        <div class="section">
            <h3>
                Card Layouts
            </h3>
            <div class="card-grid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            User Statistics
                        </h3>
                        <div class="card-header-actions">
                  <span class="badge badge-success">
                    Live
                  </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="stat-row">
                            <div class="stat">
                    <span class="stat-value">
                      1,234
                    </span>
                                <span class="stat-label">
                      Total Users
                    </span>
                            </div>
                            <div class="stat">
                    <span class="stat-value">
                      892
                    </span>
                                <span class="stat-label">
                      Active Today
                    </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="/users" class="card-link">
                            View all users →
                        </a>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Quick Actions
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="action-buttons">
                            <button class="btn btn-primary">
                                Create User
                            </button>
                            <button class="btn btn-secondary">
                                Import Data
                            </button>
                            <button class="btn btn-ghost">
                                Export CSV
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Recent Activity
                        </h3>
                        <div class="card-header-actions">
                  <span class="badge badge-warning">
                    3 new
                  </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="activity-list">
                            <li class="activity-item">
                    <span class="activity-icon">
                      👤
                    </span>
                                <span class="activity-text">
                      New user registered
                    </span>
                                <span class="activity-time">
                      2 min ago
                    </span>
                            </li>
                            <li class="activity-item">
                    <span class="activity-icon">
                      📦
                    </span>
                                <span class="activity-text">
                      Order #1234 shipped
                    </span>
                                <span class="activity-time">
                      15 min ago
                    </span>
                            </li>
                            <li class="activity-item">
                    <span class="activity-icon">
                      💬
                    </span>
                                <span class="activity-text">
                      New comment on post
                    </span>
                                <span class="activity-time">
                      1 hour ago
                    </span>
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <a href="/activity" class="card-link">
                            View all activity →
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="section">
            <h3>
                Modal Layout
            </h3>
            <p>
                Modals also use the slot pattern for flexible content:
            </p>
            <div class="modal-preview">
                <div id="delete-modal" class="modal">
                    <div class="modal-backdrop">
                    </div>
                    <div class="modal-dialog">
                        <div class="modal-header">
                            <h4 class="modal-title">
                                Confirm Deletion
                            </h4>
                            <button class="modal-close" aria-label="Close">
                                ×
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>
                                Are you sure you want to delete this item? This action cannot be undone.
                            </p>
                            <div class="modal-warning">
                    <span class="warning-icon">
                      ⚠️
                    </span>
                                <span>
                      This will permanently remove all associated data.
                    </span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-ghost">
                                Cancel
                            </button>
                            <button class="btn btn-danger">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section">
            <h3>
                How It Works
            </h3>
            <pre class="code-block">
<code>// Define a layout function with typed slot parameters
/**
 * @param string $pageTitle
 * @param list&lt;Element&gt; $headerSlot
 * @param list&lt;Element&gt; $sidebarSlot
 * @param list&lt;Element&gt; $mainSlot
 * @param list&lt;Element&gt; $footerSlot
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
);</code></pre>
        </div>
    </main>
    <footer class="dashboard-footer">
        <p>
            © 2024 Elem Examples. Built with type-safe PHP.
        </p>
        <div class="footer-links">
            <a href="https://github.com/epic-64/elem">
                GitHub
            </a>
            <span class="separator">
            •
          </span>
            <a href="/docs">
                Documentation
            </a>
            <span class="separator">
            •
          </span>
            <a href="/examples">
                Examples
            </a>
        </div>
    </footer>
</div>
</body>
</html>