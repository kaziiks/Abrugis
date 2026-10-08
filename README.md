# Abrugis

A Laravel application for a Latvian paving business. Visitors can browse completed projects and customer reviews, calculate an indicative paving estimate, submit applications, track application status, and leave a review after a project is completed. Administrators manage applications and portfolio entries through the admin panel.

## Requirements

- PHP 8.3 or newer with the extensions required by Laravel
- Composer
- Node.js and npm
- MySQL or MariaDB

## Local installation

The example configuration uses MySQL. Create the application database:

```sql
CREATE DATABASE abrugis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Copy `.env.example` to `.env` (`Copy-Item .env.example .env` in PowerShell), set `ADMIN_PASSWORD` to a unique value of at least 12 characters, and run the setup script:

```sh
composer setup
php artisan db:seed
php artisan storage:link
php artisan serve
```

The setup script installs dependencies, creates the application key, runs migrations, and builds frontend assets. Open the URL printed by `php artisan serve`. The seeders add the paving catalog, sample portfolio projects, and an administrator account. The seeded account email is configured with `ADMIN_EMAIL`; change it from the example value for any non-local environment. Seeded demo data is intended for development, not as production content.

The schema migration renames the previous Latvian table, column, and foreign-key names in place, preserving existing records.

The `public` filesystem disk is used for portfolio uploads. `php artisan storage:link` makes those uploads available to the website. Seeded portfolio images are registered only when their files exist; projects without an available image use the site's built-in placeholder.

## Configuration

- Paving material prices are maintained in `paving_types` and populated by `PavingTypeSeeder`. The calculator accepts a catalog item ID and looks up its price on the server; it never uses a price sent by the browser.
- Application dates are consultation appointments, not construction-work schedules. The calendar reserves a consultation date only after an administrator approves it; project duration is agreed separately with the client.
- Base preparation prices, old-surface removal price, and the maximum calculator area are in `config/abrugis.php`.
- Administrator seed credentials are supplied with `ADMIN_EMAIL` and `ADMIN_PASSWORD`; do not commit `.env` or reuse a local password in production.
- Application emails use the Laravel mail configuration. Set `MAIL_MAILER`, SMTP credentials when applicable, and `MAIL_ADMIN_ADDRESS` to deliver notifications. The default local mailer writes messages to the log.
- Account registration sends an email verification link; clients must verify before submitting or viewing applications. Password reset and verification emails require a working mailer. With the default local `log` mailer, inspect `storage/logs/laravel.log` for links.
- Calculator results can be carried into an application. The server stores a price snapshot with the application so later catalog price changes do not alter the estimate that was submitted.
- Portfolio image uploads are limited to 10 JPEG, PNG, or WebP images, at 5 MB each.

## Tests and frontend build

Run the automated test suite:

```sh
php artisan test
```

The test database is configured separately from the application database in `phpunit.xml`: by default, tests connect to a local MySQL database named `abrugis_testing` as `root` with no password. Create that database or update the PHPUnit environment values to match your local test database. The configured test administrator password is only a test fixture.

Build the JavaScript and CSS assets with:

```sh
npm run build
```

For local frontend development, run `npm run dev` in a separate terminal while `php artisan serve` is running.

## Main application areas

- `app/Http/Controllers` — public, application, calculator, and administration requests
- `app/Models` — paving catalog, portfolio, application, user, and review data
- `database/migrations` and `database/seeders` — schema and initial catalog/demo records
- `resources/views` — Blade pages
- `resources/js` and `resources/css` — Vite frontend sources
- `public/js` and `public/css` — static website assets
