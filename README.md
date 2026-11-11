# Renzo Restaurant Website & Admin CMS

Custom PHP 8.4 + MySQL (PDO) restaurant website and OTP-protected admin CMS structured for minimal-friction migration to Laravel.

## Requirements
- PHP 8.4+ (`PDO`, `pdo_mysql`, `gd`, `fileinfo`, `openssl`, `mbstring`)
- MySQL 8.0+ / MariaDB
- Apache (`mod_rewrite`, `mod_headers`, `mod_expires`) or Nginx on cPanel shared hosting
- Composer 2.x

## Local Setup (Laragon / Apache)
1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Copy `.env.example` to `.env` and configure `APP_URL`, `APP_TIMEZONE`, `DB_*`, and `MAIL_*` SMTP credentials.
3. Create the MySQL database (`utf8mb4_unicode_ci`) and import migrations in order:
   - `database/migrations/2026_11_10_000001_create_admins_table.sql` through `2026_11_10_000013_create_settings_table.sql`
4. Optionally import starter settings, tags, and the 3 `"Opening soon"` launch slider slides:
   - `database/seeders/initial_seed.sql`
5. Insert your first Owner admin user into `admins`:
   ```sql
   INSERT INTO admins (name, email, role, is_active)
   VALUES ('Owner Name', 'owner@example.com', 'owner', 1);
   ```
6. Point your Apache/cPanel document root to `/public`.

## Verification Suite
Run the automated PHP verification suite:
```bash
php tests/run.php
```

## CMS Media Guide
Recommended image dimensions, framing rules, automatic WebP optimization limits, and YouTube gallery guidelines are documented in [docs/cms-media-guide.md](docs/cms-media-guide.md).

