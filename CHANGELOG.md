# Changelog

## 0.5.0

### Added

- **`tap()` method** - Tap into an element for imperative modifications without breaking the fluent chain
  ```php
  div(class: 'card')->tap(function ($el) use ($isAdmin) {
      if ($isAdmin) {
          $el->class('admin');
          $el->data('role', 'administrator');
      }
  })
  ```

- **`when()` method** - Conditionally apply modifications based on a boolean condition
  ```php
  div(class: 'card')
      ->when($isAdmin, fn($el) => $el->class('admin'))
      ->when($isActive, fn($el) => $el->class('active'))
  ```

- **`append()` method** - Alias for `__invoke()` to add children, useful inside `tap()` callbacks
  ```php
  div()->tap(function ($el) use ($items) {
      foreach ($items as $item) {
          $el->append(span(text: $item));
      }
  })
  ```

- **Closure support in `__invoke()`** - Pass closures directly as children for imperative child generation
  ```php
  div(class: 'container')(
      function ($el) use ($items) {
          foreach ($items as $item) {
              $el->append(span(text: $item));
          }
      }
  )
  ```

### Documentation

- Added new [Imperative Style](docs/imperative-style.md) documentation
- Updated README with imperative style examples
