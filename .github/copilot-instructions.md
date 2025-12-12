# Copilot / AI Agent Instructions — services-booking

Guidance for AI coding agents working on this repository. Be concise and follow patterns already in place.

## Quick start
- This is a Laravel 11 app (PHP >= 8.2). Use `composer install`, copy `.env.example` to `.env`, run `php artisan key:generate` and set DB credentials.
- Typical local dev commands:
  - composer install
  - php artisan migrate --graceful
  - php artisan db:seed
  - php artisan serve
  - php artisan test  (or vendor/bin/phpunit tests/Feature)
- For fast CI/dev runs, the repo uses a `database/database.sqlite` file; composer post-create script can set it up.

## Big picture architecture (how code is organized)
- Thin Controllers -> Services (business logic) -> Repositories (data access) -> Eloquent Models.
  - Controllers: `app/Http/Controllers/*`
  - Services: `app/Services/*` (preferred location for business logic)
  - Repositories: `app/Repositories/*` (DB queries live here; extend `BaseRepository`)
  - Models: `app/Models/*` (Eloquent models)
- Routes: `routes/web.php` use middleware `auth`, `active`, and `role` and follow naming schemes (e.g. `provider.services.create`).
- Migrations in `database/migrations/*` are canonical DB schema (don’t rewrite history; add new migrations for schema changes).
- Views in `resources/views/` organized by role (`provider/`, `admin/`, `customer/`, `public/`) and use Laravel Blade.

## Project conventions & patterns (essential for being productive)
- Use dependency injection: inject Services/Repositories through controllers' constructors.
  - Example: `app/Http/Controllers/Provider/ServiceController.php` injects `ProviderManagementService` and `ProviderServiceRepository`.
- Repositories implement reusable query methods; tests and controllers use repository methods instead of direct Eloquent queries.
  - Example: `ServiceRepository::getActiveWithCategory()` and `getPaginatedForAdmin()`.
- Keep controllers thin: create new methods in the Service layer when behavior grows.
- Validation: Use Form Request classes (`app/Http/Requests/*`) for validation rules.
- View folder naming: prefer hyphenated folder names for multi-word pages (e.g., `time-slots`) and ensure controller views point to the correct path (`provider.time-slots.index`).
- When adding a `provider` page, add route under `routes/web.php` with middleware and `provider.` name prefix.
- Password hashing: Auth logic uses `Hash::make` and implements a fallback/rehash logic in `app/Services/AuthService.php`. When verifying or re-hashing passwords, follow this pattern and respect `bcrypt` default.

## Testing conventions
- Feature tests live under `tests/Feature/*`. Use HTTP requests to assert behavior end-to-end.
- Use factories in `database/factories/*` and seeders `database/seeders` to set up test data.
- Run tests: `php artisan test` or `vendor/bin/phpunit tests/Feature --color=always`.
- Prefer adding tests for new behavior along with any new controller/service/repository changes.

## Common tasks and examples
- Adding a new resource (e.g., Service Category):
  1. Create a migration in `database/migrations`.
  2. Add Eloquent Model under `app/Models/`.
  3. Add Repository in `app/Repositories/`, extend `BaseRepository` and add any domain-specific query methods.
  4. Add a Service method in `app/Services/` to encapsulate business logic.
  5. Add controller endpoint in `app/Http/Controllers/*` and add a route under `routes/web.php`.
  6. Add views under `resources/views/admin/*` (or role-appropriate path).
  7. Add feature tests in `tests/Feature/*` and update seeders/factories as required.

## Debugging tips
- Logs: `storage/logs/laravel.log` provides runtime exceptions and stack traces.
- Use `php artisan tinker` for interactive testing.
- Use `php artisan migrate --graceful` when running migrations against incomplete or partially seeds.
- Searching for `View [provider.time_slots.index] not found` indicates view folder naming mismatch between controller and view files.

## Non-trivial project-specific gotchas
- Use `Hash::driver()` fallback approaches carefully when verifying legacy password hashes; `AuthService::login()` outlines a safe pattern (try other drivers and rehash on match).
- Avoid editing old migrations after they have been committed and used; add new migrations to adjust schema.
- Keep `Services` for logic that could be reused across controllers (e.g., `ProviderManagementService` handles adding/updating provider services/time slots/locations).
- Views sometimes expect both `providerProfile` and a legacy `profile` variable — pass both if adding to a controller's return data.
- When creating new UI pages, keep to the templates under `resources/views/{provider|customer|admin|public}` and reuse partials where available.

## Files to inspect first (quick reference)
- `README.md` — project setup and high-level architecture
- `routes/web.php` — endpoints, naming, middleware
- `app/Services/AuthService.php` — password hash, login flow patterns
- `app/Repositories/BaseRepository.php` — repository API to follow
- `app/Repositories/ServiceRepository.php` — example advanced queries
- `app/Http/Controllers/Provider/ServiceController.php` — best practices for thin controllers and service injection
- `database/migrations` — canonical DB schema; update `docs/erd.md` after schema changes
- `tests/Feature` — examples of end-to-end tests and set-up patterns

## PR / Commit & code-style conventions
- Commit messages: use `area(scope): short description` pattern when possible (e.g., `feat(provider): add ...`, `fix(auth): rehash fallback`)
- Use `laravel/pint` for code-style checks if running lints locally.
- Add or update tests for behavioral changes; do not commit features without tests when possible.

## Final note
- If you modify DB schema, update `docs/erd.md` and `docs/database-schema.md` (they are used as source-of-truth for the data model). If you touch password hashing or login flows, add tests to validate any fallback/rehash logic.

---
Please review and suggest any project-specific edge cases to add (e.g., CI steps, local dev shortcuts, or branching strategy) and I’ll iterate.