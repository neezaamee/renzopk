# Renzo Project Constitution

> Custom PHP 8.4 restaurant website structured for minimal-friction migration to Laravel.
> Locked rules below; unspecified details are marked `TBD` and must not be assumed.

## 1. Tech Stack

- **Language & Runtime:** PHP 8.4 (detected local CLI: 8.4.12 with `PDO`, `pdo_mysql`, `fileinfo`, `openssl`, `mbstring`, `sodium`)
- **Database:** MySQL via `PDO` with prepared statements only (server version, host, DB name: `TBD`)
- **Web Server / Hosting:** Apache/Nginx on cPanel shared hosting; no Node.js; no build step (local web server setup: `TBD`)
- **Dependencies:** Composer only — `PHPMailer`, `Dotenv`, `Dompdf` (exact package versions: `TBD`; do not install unless asked)
- **Front Controller:** Single entry point at `/public/index.php`
- **Scheduling:** No cron jobs; time-based logic (e.g., deal expiry) handled by SQL queries at request time

## 2. Architecture Rules (Laravel-Ready)

- **Controllers (`/app/Controllers`):** One controller class per resource; no business logic
- **Models (`/app/Models`):** One model class per table, all extending a base `Model` using `PDO`
- **Services (`/app/Services`):** All business logic lives in `/app/Services`, never in controllers or views
- **Views (`/app/Views`):** Plain PHP templates using `<?= $var ?>` (never `<?php echo`); output-escaping helper: `TBD`
- **Routing (`/routes/web.php`):** Routes defined in Laravel-style array syntax (exact array/dispatcher convention: `TBD`)
- **Config (`.env`):** Laravel key names (`DB_HOST`, `MAIL_HOST`, etc.; `.env` is local-only and must not be edited automatically)
- **Migrations (`/database/migrations`):** SQL files named `YYYY_MM_DD_HHMMSS_create_table_name_table.sql`
- **Autoloading / PSR-4 Namespace:** `TBD`
- **Application Scope (Resources, Tables, Roles, Locale/Timezone):** `TBD`

## 3. Security Baseline (Non-Negotiable)

- **CSRF:** CSRF token required on every `POST` form
- **OTP:** Codes hashed with `password_hash()`, rate-limited to 5 attempts / 10 min, 10 min expiry (target flows & delivery channel: `TBD`)
- **Sessions:** Cookies set with `httponly`, `secure`, and `samesite=Lax` (local HTTP dev handling: `TBD`)
- **File Uploads:** Validated by MIME type and file size (allowed types & max size: `TBD`); PHP execution disabled in `/storage/uploads`
- **HTTPS:** Enforced via `.htaccess` (local dev / Nginx rules if applicable: `TBD`)

## 4. Operational Constraints

- Do not install dependencies unless asked.
- Do not run destructive commands.
- Do not edit secrets or local-only files.
- Keep all memory files concise.
- Do not invent facts; mark unknowns as `TBD`.

