# Composition & Dynamism

This is where Elem really shines. Unlike templates where you're limited to template syntax, Elem gives you the full power of PHP: enums for type-safe variants, functions for reusable components, and native control flow for conditional rendering.

## Type-Safe Variants with Enums

No more typos like `'sucess'` or `'waning'` - the compiler catches them:

```php
use Epic64\Elem\Element;
use function Epic64\Elem\span;
use function Epic64\Elem\div;

enum BadgeVariant: string
{
    case Default = 'default';
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';
    case Error = 'error';
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

function badge(string $text, BadgeVariant $variant = BadgeVariant::Default): Element
{
    return span(class: "badge badge-{$variant->value}", text: $text);
}

function alert(string $message, AlertType $type = AlertType::Info): Element
{
    return div(class: "alert alert-{$type->value}")(
        span(class: 'alert-icon', text: $type->icon()),
        span(class: 'alert-message', text: $message)
    );
}

// IDE autocomplete shows you all valid options
badge('Active', BadgeVariant::Success);
alert('Something went wrong!', AlertType::Error);
```

## Composable Components

Components are just functions. Nest them, parameterize them, compose them:

```php
use Epic64\Elem\Element;
use function Epic64\Elem\div;
use function Epic64\Elem\span;
use function Epic64\Elem\h;

enum Trend: string
{
    case Up = 'up';
    case Down = 'down';
    case Neutral = 'neutral';

    public function icon(): string
    {
        return match ($this) {
            self::Up => '📈',
            self::Down => '📉',
            self::Neutral => '➡️',
        };
    }
}

readonly class User
{
    public function __construct(
        public string $name,
        public string $email,
        public UserRole $role,
        public bool $active,
    ) {}
}

readonly class Stat
{
    public function __construct(
        public string $label,
        public int $value,
        public Trend $trend,
    ) {}
}

function avatar(string $name): Element
{
    $initials = implode('', array_map(
        fn($word) => mb_substr($word, 0, 1),
        array_slice(explode(' ', $name), 0, 2)
    ));

    return div(class: 'avatar', text: strtoupper($initials));
}

function userCard(User $user): Element
{
    return div(class: 'user-card')(
        avatar($user->name),
        div(class: 'user-info')(
            span(class: 'user-name', text: $user->name),
            span(class: 'user-email', text: $user->email)
        ),
        badge($user->role->value, $user->role->badge()),
        badge($user->active ? 'Active' : 'Inactive', 
              $user->active ? BadgeVariant::Success : BadgeVariant::Error)
    );
}

function statCard(Stat $stat): Element
{
    return div(class: "stat-card stat-{$stat->trend->value}")(
        span(class: 'stat-value', text: (string) $stat->value),
        span(class: 'stat-label', text: $stat->label),
        span(class: 'stat-trend', text: $stat->trend->icon())
    );
}
```

## Data Transformation

Filter, map, and transform your data with native PHP or the fluent `list_of()` helper:

```php
use function Epic64\Elem\div;
use function Epic64\Elem\list_of;

enum UserRole: string
{
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    public function badge(): BadgeVariant
    {
        return match ($this) {
            self::Admin => BadgeVariant::Primary,
            self::Editor => BadgeVariant::Warning,
            self::Viewer => BadgeVariant::Default,
        };
    }
}

/** @var list<User> $users */
$users = [
    new User('Alice', 'alice@example.org', UserRole::Admin, active: true),
    new User('Bob', 'bob@example.org', UserRole::Editor, active: true),
    new User('Charlie', 'charlie@example.org', UserRole::Viewer, active: false),
];

// Show only active users
div(class: 'active-users')(
    list_of($users)
        ->filter(fn(User $user) => $user->active)
        ->map(fn(User $user) => userCard($user))
);

// Filter by role - no typos possible!
div(class: 'admin-users')(
    list_of($users)
        ->filter(fn(User $u) => $u->role === UserRole::Admin)
        ->map(fn(User $u) => userCard($u))
);
```

## Conditional Rendering

It's just PHP - use ternaries, if statements, or match expressions:

```php
use function Epic64\Elem\div;
use function Epic64\Elem\p;

readonly class CurrentUser
{
    public function __construct(
        public string $name,
        public UserRole $role,
        public int $notifications,
    ) {}

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}

$currentUser = new CurrentUser('Jane', UserRole::Admin, notifications: 3);
$outOfStockCount = count(array_filter($products, fn(Product $p) => !$p->inStock));

div(class: 'dashboard')(
    // Conditional badge
    $currentUser->notifications > 0
        ? badge("{$currentUser->notifications} new", BadgeVariant::Warning) 
        : null,

    // Admin-only content - method encapsulates the logic
    $currentUser->isAdmin() ? div(class: 'admin-panel')(
        alert('You have admin privileges.', AlertType::Warning),
        p(text: 'Manage users, view logs, configure settings.')
    ) : null,

    // Dynamic alerts based on data
    $outOfStockCount > 0
        ? alert("$outOfStockCount product(s) out of stock", AlertType::Warning)
        : alert('All products in stock!', AlertType::Success),

    // Match on enum - exhaustive checking by PHPStan
    match ($currentUser->role) {
        UserRole::Admin => badge('Administrator', BadgeVariant::Primary),
        UserRole::Editor => badge('Editor', BadgeVariant::Warning),
        UserRole::Viewer => badge('Viewer', BadgeVariant::Default),
    }
);
```

## Putting It All Together

Here's an example combining all these patterns:

```php
use function Epic64\Elem\{html, head, title, body, div, h, list_of, stylesheet};

$currentUser = new CurrentUser('Jane', UserRole::Admin, notifications: 3);

/** @var list<User> $users */
$users = $userRepository->findAll();

/** @var list<Stat> $stats */
$stats = [
    new Stat('Total Users', count($users), Trend::Up),
    new Stat('Active', count(array_filter($users, fn(User $u) => $u->active)), Trend::Up),
];

return html(lang: 'en')(
    head()(
        title(text: 'Dashboard'),
        stylesheet('/css/app.css')
    ),
    body()(
        div(class: 'dashboard')(
            // Header with conditional notification badge
            div(class: 'header')(
                h(1, text: "Welcome back, {$currentUser->name}!"),
                $currentUser->notifications > 0
                    ? badge("{$currentUser->notifications} new", BadgeVariant::Warning)
                    : null
            ),

            // Admin alert - type-safe method call
            $currentUser->isAdmin()
                ? alert('Admin mode enabled', AlertType::Info)
                : null,

            // Stats grid - fully typed Stat objects
            div(class: 'stats-grid')(
                ...array_map(fn(Stat $s) => statCard($s), $stats)
            ),

            // User list with filtering - typed throughout
            div(class: 'user-list')(
                h(2, text: 'Active Team Members'),
                list_of($users)
                    ->filter(fn(User $u) => $u->active)
                    ->map(fn(User $u) => userCard($u))
            )
        )
    )
);
```

This is the power of Elem: **your views are PHP**, so you get type safety, IDE support, refactoring, and the full expressiveness of the language. No template DSL to learn, no magic strings, no runtime surprises.
