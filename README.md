# Daurah Syariyyah — Participant Management System

[DAURAH.SYATHIBY.ID](https://daurah.syathiby.id)

A full-featured Laravel-based participant management system for Islamic study events (Daurah). Originally migrated from a pure PHP application, this system streamlines event registration, geolocation-based attendance tracking, QR code authentication, WhatsApp notifications, and certificate generation for study event organizers.

---

## Table of Contents

1. [Overview](#overview)
2. [Features](#features)
3. [Technology Stack](#technology-stack)
4. [Requirements](#requirements)
5. [Installation](#installation)
6. [Configuration](#configuration)
7. [Project Structure](#project-structure)
8. [Database Schema](#database-schema)
9. [Route Reference](#route-reference)
10. [Authentication](#authentication)
11. [Key Features In Depth](#key-features-in-depth)
12. [Deployment](#deployment)
13. [Maintenance](#maintenance)

---

## Overview

**Daurah Syariyyah** is designed for Islamic study event committees to manage participant registration, track attendance using GPS validation and QR codes, distribute teaching materials, send WhatsApp notifications, and issue completion certificates — all from a single unified platform.

The system supports two attendance modes: **geolocation-based** (validates participant position against event coordinates) and **QR-based** (static or dynamic token-based codes). It enables self-service registration for participants while giving administrators full control over event creation, session management, and confirmation workflows.

---

## Features

### Participant Features

| Feature | Description |
|---------|-------------|
| **Self-Registration** | Participants register via `/konfirmasi` without admin intervention. Fields include full name, institution/organization (default: `PRIBADI`), domicile, WhatsApp number, accommodation preference, and proof of invitation upload (PDF/image, max 1MB with browser compression). An Islamic agreement checkbox must be accepted before submission. |
| **Participant Dashboard** | Displays a large ID card with name, phone, institution, domicile, and accommodation status. Shows per-event registration status (Confirmed / Pending). Auto-displays WhatsApp group invitation link when registration is confirmed. |
| **Geolocation Attendance** | When a session is active, participants submit attendance via browser geolocation. The system validates distance against event coordinates using the Haversine formula. |
| **Material Confirmation** | Participants confirm material pickup — either once at event start or per session, depending on event configuration. |
| **Attendance History** | Full record of all attended sessions across events. |
| **Certificate Access** | Participants with complete attendance records can view and print event certificates. |

### Admin Features

| Feature | Description |
|---------|-------------|
| **Event Management** | Create events with name, date, QR mode (static/dynamic), WhatsApp group link, auto-confirm toggle, material type (none/once at start/per session), and geolocation settings (latitude, longitude, radius in meters). |
| **Session Management** | Add/remove sessions per event with session name, start time, and end time. |
| **Participant Confirmation** | List participants per event with filter capabilities. Actions include: Confirm (ACC), Send WhatsApp, Delete. Bulk confirmation (ACC All). |
| **WhatsApp Integration** | Auto-generated messages include event name, date, full session schedule, reminders, and group invitation link. Opens `wa.me` deep link. |
| **CSV Export** | Export participant lists per event as UTF-8 BOM CSV (Excel-compatible). |
| **User Management** | Full CRUD for users. CSV import/export. Edit participant profiles. User detail modal with proof of invitation preview. Sort by name, institution, created_at. Filter by search, date range. |
| **Attendance History** | Filterable attendance records across all events and sessions. |
| **QR Scan Monitor** | Real-time monitor for QR code scanning during events. |

### System Features

| Feature | Description |
|---------|-------------|
| **Dual QR Modes** | Static QR (same code for all scans) or Dynamic QR (token-based with 20-second expiration using `api.qrserver.com`). |
| **Geolocation Validation** | Haversine formula calculates real-world distance. Configurable radius per event. Toggle on/off per event. |
| **Phone Number Normalization** | Auto-formats international/domestic formats (`08xx`, `+62xx`, `62xx`) to standard Indonesian format (`08xx`). Includes CLI command `php artisan phone:reformat` for database cleanup. |
| **Auto-Invite** | Events can auto-invite users upon registration. Users see auto-invited events on their dashboard. |
| **Certificate Templates** | Per-event configurable certificate template with background image and custom font settings. |

---

## Technology Stack

| Component | Technology | Version |
|-----------|------------|---------|
| **Framework** | Laravel | ^12.0 |
| **Language** | PHP | ^8.2 |
| **Database** | MySQL | — |
| **Frontend (CSS)** | Bootstrap 5 + Font Awesome 6.4.0 | CDN |
| **Frontend (Build)** | Tailwind CSS v4 + Vite | ^4.0.0 / ^7.0.7 |
| **Authentication** | Custom session-based | — |
| **QR Generation** | api.qrserver.com | — |
| **Geolocation** | Browser Geolocation API + Haversine formula | — |

---

## Requirements

- **PHP**: ^8.2
- **Composer**: Latest stable
- **Node.js**: ^18.x or ^20.x
- **NPM**: Latest stable
- **MySQL**: ^5.7 or ^8.0
- **Web Server**: Apache/Nginx (for production) or Laravel Valet/Laragon (for local development)

---

## Installation

### 1. Clone / Copy Project

```bash
cd /path/to/daurah-laravel
```

### 2. Install PHP Dependencies

```bash
composer install
```

laragon :
```bash
C:\laragon\bin\php\php-8.2.27-nts-Win32-vs16-x64\php.exe C:\laragon\bin\composer\composer.phar install
```

### 3. Install Node Dependencies

```bash
npm install
```

### 4. Configure Environment

```bash
# Copy environment template
cp .env.example .env

# Edit .env with your database credentials and app settings
# See Configuration section below
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Run Database Migrations

```bash
php artisan migrate
```

> **Note**: If migrating from the legacy pure PHP system, ensure the MySQL database `syathiby_daurah` already exists. Fresh migration will create all tables. If continuing from the old system, skip migration and use existing tables.

### 7. Build Frontend Assets

```bash
npm run build
```

### 8. Start Development Server

```bash
php artisan serve
```

Access the application at `http://localhost:8000`.

```bash
C:\laragon\bin\php\php-8.2.27-nts-Win32-vs16-x64\php.exe artisan serve --port 8001
```

---

## Configuration

### Environment Variables (.env)

Key variables that must be configured:

```env
APP_NAME="Dauroh Syariyyah"
DAURAH_NAME="Daurah Syariyyah ke-6"   # Judul Daurah di action bar (bisa diubah sesuai event)
APP_ENV=local              # Use 'production' in live environment
APP_KEY=                   # Generated via php artisan key:generate
APP_DEBUG=true             # Set to 'false' in production
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=syathiby_daurah
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

SESSION_DRIVER=file         # Or 'redis' for production
SESSION_LIFETIME=120

CACHE_STORE=file           # Or 'redis' for production
QUEUE_CONNECTION=database  # Or 'redis' for production
```

### Event Geolocation Setup

For geolocation-based attendance, set these fields when creating an event:

```env
# In event creation form:
# Latitude:   e.g., -6.2088 (Jakarta example)
# Longitude:  e.g., 106.8456
# Radius:     e.g., 100 (meters)
```

---

## Project Structure

```
daurah-laravel/
├── app/
│   ├── Console/Commands/      # Artisan CLI commands (phone:reformat)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/          # LoginController, KonfirmasiController
│   │   │   ├── Admin/         # Dashboard, Event, User, Konfirmasi, History controllers
│   │   │   └── User/          # Dashboard, Profile, Certificate controllers
│   │   └── Middleware/
│   │       └── AuthMiddleware.php  # Session-based authentication
│   ├── Models/                # User, Event, EventSession, EventRegistration,
│   │                          # Attendance, Material, QrToken, Setting
│   └── Services/              # WhatsAppService, GeolocationService
├── bootstrap/app.php          # Middleware registration
├── config/                    # Laravel config files
├── database/
│   ├── factories/
│   ├── migrations/           # All database migrations
│   └── seeders/
├── public/
│   ├── index.php             # Application entry point
│   └── assets/               # Compiled CSS/JS
├── resources/
│   └── views/
│       ├── admin/            # Admin panel views (dashboard, events, users, konfirmasi, history)
│       ├── auth/             # Login, registration views
│       ├── user/             # Participant dashboard, profile, certificate
│       ├── scan/             # QR scan success/error pages
│       ├── monitor/          # QR scan monitor
│       └── layouts/          # Blade layout templates
├── routes/
│   └── web.php               # All web routes
├── storage/
│   ├── app/                  # Stored files (certificates, etc.)
│   ├── framework/            # Compiled views, cache
│   └── logs/                 # Application logs
├── tests/                    # PHPUnit test suite
├── .env                      # Environment configuration
├── .env.example              # Environment template
├── composer.json             # PHP dependencies
├── package.json              # Node dependencies
├── vite.config.js            # Vite build configuration
└── README.md                 # This file
```

---

## Database Schema

| Table | Description | Key Fields |
|-------|-------------|------------|
| `users` | Participants | `name`, `nohp` (phone), `lembaga` (institution), `domisili` (domicile), `menginap` (accommodation), `role` (admin/user), `bukti_undangan` (file path), `created_at`, `updated_at` |
| `events` | Event information | `nama`, `tanggal`, `qr_mode` (static/dynamic), `latitude`, `longitude`, `radius`, `geolocation_enabled`, `group_link` (WhatsApp), `auto_confirm`, `material_type`, `created_at`, `updated_at` |
| `event_sessions` | Sessions per event | `event_id`, `nama_sesi`, `jam_mulai`, `jam_selesai` |
| `event_registrations` | Participant registrations | `user_id`, `event_id`, `status` (pending/confirmed), `auto_invite` |
| `attendance` | Attendance records | `user_id`, `event_id`, `session_id`, `waktu_scan`, `latitude`, `longitude`, `materi_confirmed` |
| `materials` | Teaching materials | `event_id`, `nama`, `tipe` |
| `qr_tokens` | Dynamic QR tokens | `event_id`, `token`, `expires_at`, `used` |
| `settings` | Application settings | `key`, `value` |

---

## Route Reference

### Public Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET/POST | `/login` | Session-based login with phone number |
| GET | `/logout` | End user session |
| GET/POST | `/konfirmasi` | Self-service participant registration |

### Authenticated Participant Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/dashboard` | Participant dashboard |
| POST | `/dashboard/absen` | Submit geolocation attendance |
| POST | `/dashboard/materi` | Confirm material pickup |
| GET/POST | `/profile` | View/update profile |
| GET | `/certificate` | View/print certificates |

### Admin Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/admin/dashboard` | Admin statistics overview |
| GET | `/admin/events` | List all events |
| GET/POST | `/admin/events/create` | Create new event |
| GET/POST | `/admin/events/{id}/edit` | Edit event |
| POST | `/admin/events/{id}/delete` | Delete event |
| POST | `/admin/events/{id}/sessions` | Add session to event |
| POST | `/admin/events/{id}/invite` | Invite users to event |
| POST | `/admin/events/{id}/auto-confirm` | Toggle auto-confirm |
| GET | `/admin/konfirmasi` | List all registrations |
| GET | `/admin/konfirmasi/{eventId}` | List registrations per event |
| POST | `/admin/konfirmasi/{id}/acc` | Confirm a registration |
| GET | `/admin/konfirmasi/{id}/wa` | Send WhatsApp notification |
| POST | `/admin/konfirmasi/{id}/hapus` | Delete registration |
| POST | `/admin/konfirmasi/{eventId}/acc-all` | Confirm all pending registrations |
| GET | `/admin/konfirmasi/{eventId}/export` | Export CSV with BOM |
| GET | `/admin/users` | List all users |
| GET/POST | `/admin/users/create` | Create user manually |
| GET/POST | `/admin/users/{id}/edit` | Edit user |
| POST | `/admin/users/import` | Import CSV |
| GET | `/admin/users/export` | Export CSV |
| GET | `/admin/history` | Attendance history with filters |

### QR / Scan Routes

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/monitor/{eventId}` | QR scan monitor |
| GET | `/monitor/{eventId}/qr` | Generate QR code (static or dynamic) |
| GET | `/download-qr/{eventId}` | Download QR code as PNG |
| GET | `/proses-scan/{eventId}` | Process QR scan for attendance |

---

## Authentication

The system uses a **custom session-based authentication** — not Laravel's built-in auth scaffolding.

- **Login identifier**: Phone number (no password required)
- **Session storage**: `user_id` and `role` stored in session
- **Middleware**: Custom `AuthMiddleware` (registered as `auth` alias in `bootstrap/app.php`)
- **Roles**: `admin` or `user`

**Login Flow**:
1. User enters registered phone number at `/login`
2. System looks up user by phone number
3. On success, `user_id` and `role` are stored in session
4. Admin users are redirected to `/admin/dashboard`, regular users to `/dashboard`

**Phone Number Normalization**:
The system auto-converts various phone formats to standard Indonesian format:
- `08123456789` → `08123456789`
- `+628123456789` → `628123456789`
- `628123456789` → `628123456789`
- `8123456789` → `08123456789`

---

## Key Features In Depth

### Geolocation-Based Attendance

The system uses the **Haversine formula** to calculate the great-circle distance between participant coordinates and event coordinates.

```
Distance = 2 × R × arcsin(√(sin²(Δlat/2) + cos(lat1) × cos(lat2) × sin²(Δlon/2)))
```

Where R = 6,371 km (Earth's radius).

Participants must have browser geolocation enabled. The system compares their coordinates against the event's center point within the configured radius (in meters). Attendance is only recorded if the participant is within range.

**Configuration per event**:
- Enable/disable geolocation requirement
- Set center coordinates (latitude/longitude)
- Set validation radius (meters)

### QR Code Attendance

**Static Mode**: A fixed QR code is generated for the event. All scans use the same code. Suitable for small events with low fraud risk.

**Dynamic Mode**: A new QR token is generated every 20 seconds. Tokens are stored in `qr_tokens` table with expiration timestamps. Each token can only be used once. Provides higher security against screenshot/forward abuse.

QR codes are generated via `api.qrserver.com` and displayed on the scan monitor at `/monitor/{eventId}`.

### WhatsApp Integration

The system generates `wa.me` deep links for WhatsApp outreach. Admin can send templated messages to participants from the confirmation panel. Messages include:
- Event name and date
- Full session schedule (name, start time, end time)
- Reminder notes
- WhatsApp group invitation link

### Certificate Generation

Certificates are template-based per event. Requirements:
- Event must have a certificate template image uploaded
- Participant must have attended all sessions
- System renders participant name onto the template with configurable font settings

---

## Deployment

### Shared Hosting (cPanel) — Complete Guide

This section covers deployment to a typical shared hosting environment with cPanel.

---

#### Step 1: Server Requirements

Ensure your hosting environment meets these requirements:

- **PHP**: ^8.2 with extensions: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`
- **Database**: MySQL ^5.7 or ^8.0
- **Web Server**: Apache with `mod_rewrite` enabled
- **Storage**: `storage/` directory must be writable by the web server
- **SSH Access**: Recommended but optional (many tasks can be done via File Manager)

---

#### Step 2: Local Preparation

Before uploading to hosting, prepare the project on your local machine:

```bash
cd C:\laragon\www\daurah-laravel

# Install all dependencies locally
composer install
npm install

# Build frontend assets for production
npm run build

# Verify no errors
php artisan route:list
```

**Important**: Edit `.env` for production before uploading:

```env
APP_NAME="Dauroh Syariyyah"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_username_daurah
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_secure_password

MAINTENANCE_PASSWORD=your-secure-maintenance-password
```

---

#### Step 3: Set PHP Version in cPanel

Shared hosting often defaults to PHP 7.4, but Laravel 12 requires PHP 8.2.

1. Login to **cPanel**
2. Go to **MultiPHP Manager** (or **PHP Version Manager**)
3. Select your domain `daurah.syathiby.id`
4. Set PHP version to **ea-php82** (or highest 8.x available)
5. Click Apply

Verify via SSH:

```bash
/opt/cpanel/ea-php82/root/usr/bin/php --version
# Should show PHP 8.2.x
```

---

#### Step 4: Upload Project Files

**Option A — Via File Manager / FTP:**

1. Compress all project files (excluding `node_modules/`, `.git/`, `vendor/` if possible) to `daurah-laravel.zip`
2. Upload to `public_html/` or your subdomain folder
3. Extract via File Manager
4. Move `public/` contents to root if needed (some hosts require Laravel files inside `public_html/` directly)

**Option B — Via SSH (if available):**

```bash
cd ~
git clone your-repo-url daurah-laravel
ln -s ~/daurah-laravel/public ~/public_html/daurah
```

**Important**: Ensure the following structure:
```
public_html/
├── index.php           # Laravel entry point (from public/)
├── .htaccess           # Rewrite rules
├── build/              # Compiled assets (from public/build/)
├── storage/            # Writable storage
├── vendor/             # PHP dependencies
└── ...
```

---

#### Step 5: Setup Database in cPanel

1. Go to **MySQL Databases** in cPanel
2. Create a new database (e.g., `cpaneluser_daurah`)
3. Create a MySQL user and assign ALL privileges
4. Open **phpMyAdmin** and import your database, or proceed to Step 6 for fresh migration

---

#### Step 6: Install Composer Dependencies on Server

If you did not include `vendor/` in your upload, install dependencies on the server.

First, find the PHP 8.2 path on your server:

```bash
ls /opt/cpanel/
# Look for ea-php82
```

**Download Composer phar for PHP 8.2:**

```bash
cd ~
curl -sS https://getcomposer.org/installer | /opt/cpanel/ea-php82/root/usr/bin/php -d allow_url_fopen=On
mv composer.phar ~/composer82
chmod +x ~/composer82
```

**Install dependencies:**

```bash
cd ~/daurah.syathiby.id
~/composer82 install --no-dev --optimize-autoloader --ignore-platform-reqs
```

If the lock file has PHP version conflicts, use:

```bash
~/composer82 update --no-dev --optimize-autoloader --ignore-platform-reqs
```

---

#### Step 7: Set Directory Permissions

Via File Manager or SSH:

```
storage/                  → 755
bootstrap/cache/          → 755
public/                   → 755
storage/app/public/bukti_undangan/  → 775 (for proof of invitation file uploads)
```

SSH commands:

```bash
cd ~/daurah.syathiby.id
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/app/public/bukti_undangan
```

---

#### Step 7b: Setup Storage Symlink

Proof of invitation files are served via PHP route `/file/bukti-undangan/{filename}` (symlink not required).

**Symlink still required for backward compatibility:**

```bash
cd ~/daurah.syathiby.id/public
ln -s ../storage/app/public storage
```

**Verifikasi:**
```bash
ls -la public/storage
# Should show: storage -> ../storage/app/public
```

---

#### Step 8: Run Database Migration

Via SSH with PHP 8.2:

```bash
/opt/cpanel/ea-php82/root/usr/bin/php ~/daurah.syathiby.id/artisan migrate --force
```

**New Migration - bukti_undangan column:**
If migration fails because column already exists, skip this migration or edit manually:

```sql
ALTER TABLE users ADD COLUMN bukti_undangan VARCHAR(255) NULL AFTER menginap;
```

**Ensure column `bukti_undangan` exists in `users` table.**

Or via the web-based Maintenance endpoints (see Maintenance section below).

---

#### Step 9: Configure .htaccess for PHP 8.2

In `public_html/` or your domain root, create or edit `.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Redirect to HTTPS (if not already)
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Laravel routes
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php/$1 [L]
</IfModule>

# Set PHP version for this directory
<IfModule mod_suphp.c>
    AddHandler application/x-httpd-alt-php82 .php .php5 .php7 .php8
</IfModule>
```

---

#### Step 10: SSL Certificate

Most cPanel hosts provide **AutoSSL** or similar. Enable it:

1. Go to **SSL/TLS** in cPanel
2. Click "Manage SSL Sites"
3. Install AutoSSL for your domain

Or use Let's Encrypt manually if AutoSSL is not available.

---

#### Step 11: Final Verification

```bash
# Clear all caches
/opt/cpanel/ea-php82/root/usr/bin/php ~/daurah.syathiby.id/artisan config:clear
/opt/cpanel/ea-php82/root/usr/bin/php ~/daurah.syathiby.id/artisan view:clear
/opt/cpanel/ea-php82/root/usr/bin/php ~/daurah.syathiby.id/artisan cache:clear

# Verify routes
/opt/cpanel/ea-php82/root/usr/bin/php ~/daurah.syathiby.id/artisan route:list
```

Test in browser:
```
https://daurah.syathiby.id/
```

---

### VPS / Cloud Server Deployment

For VPS or cloud servers (DigitalOcean, AWS, etc.), the steps are similar but with more control:

```bash
# SSH into your server
ssh root@your-server-ip

# Install required packages
apt update && apt upgrade -y
apt install -y php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip php8.2-gd unzip git

# Install Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Clone or upload project
git clone your-repo-url /var/www/daurah-laravel

# Configure
cd /var/www/daurah-laravel
cp .env.example .env
# Edit .env with production values

# Install dependencies
composer install --no-dev --optimize-autoloader

# Build frontend
npm install
npm run build

# Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data /var/www/daurah-laravel

# Configure Nginx (see Nginx config in Local Installation section)
# Configure SSL with certbot
```

---

## Maintenance

### Web-Based Maintenance Endpoints

The application includes secure maintenance endpoints accessible via HTTP without SSH access. All endpoints require authentication via the `MAINTENANCE_PASSWORD` environment variable.

**Add to `.env`:**
```env
MAINTENANCE_PASSWORD=your-secure-password-here
```

**Authentication Methods:**

| Method | Example |
|--------|---------|
| **X-Maintenance-Password Header** (Recommended) | `curl -H "X-Maintenance-Password: your-password" https://daurah.syathiby.id/maintenance/clear-config` |
| **Query Parameter** | `https://daurah.syathiby.id/maintenance/migrate?key=your-password` |

**Available Endpoints:**

| Method | URI | Description |
|--------|-----|-------------|
| GET | `/maintenance/clear-config` | `php artisan config:clear` |
| GET | `/maintenance/clear-view` | `php artisan view:clear` |
| GET | `/maintenance/optimize` | `php artisan config:cache && route:cache && view:cache` |
| GET | `/maintenance/clear-all` | Clear all caches (config, view, cache, routes) |
| GET | `/maintenance/migrate` | `php artisan migrate --force` |
| GET | `/maintenance/migrate-rollback` | `php artisan migrate:rollback --force` |
| GET | `/maintenance/migrate-status` | Show migration status (JSON) |
| GET | `/maintenance/db-status` | Show database tables and row counts |
| GET | `/maintenance/storage-link` | `php artisan storage:link` - Create storage symlink (for backward compatibility) |

**Example Usage:**

```bash
# Clear all caches
curl -H "X-Maintenance-Password: your-password" https://daurah.syathiby.id/maintenance/clear-all

# Run migrations
curl -H "X-Maintenance-Password: your-password" https://daurah.syathiby.id/maintenance/migrate

# Check database status
curl -H "X-Maintenance-Password: your-password" https://daurah.syathiby.id/maintenance/db-status

# Optimize (cache config, routes, views)
curl -H "X-Maintenance-Password: your-password" https://daurah.syathiby.id/maintenance/optimize
```

**Example Browser Access:**
```
https://daurah.syathiby.id/maintenance/clear-all?key=your-password
https://daurah.syathiby.id/maintenance/migrate?key=your-password
```

**Response Format (JSON):**
```json
{"status":"success","message":"Configuration cache cleared."}
```

Error response (401 Unauthorized):
```json
{"status":"error","message":"Unauthorized. Provide X-Maintenance-Password header or ?key= parameter."}
```

---

### CLI Maintenance Commands

```bash
# Clean old sessions
php artisan session:flush

# Reformat phone numbers in database (normalize Indonesian phone formats)
php artisan phone:reformat

# Clear logs
php artisan log:clear

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

### Database Backups

Schedule daily backups:

```bash
# Via cron - daily at 2 AM
0 2 * * * mysqldump -u your_user -p'your_password' syathiby_daurah > /home/username/backups/daurah_$(date +\%Y\%m\%d).sql
```

Or via phpMyAdmin export manually.

---

### Monitoring

- Check `storage/logs/laravel.log` for errors
- Monitor `storage/framework/cache/` directory size — clear if growing large
- Monitor MySQL slow query log for performance issues
- Monitor disk usage on `storage/` directory

---

### Updating the Application

When updating to a new version:

1. **Backup database first**
2. Upload new files (or `git pull` if using git)
3. Run `composer install --no-dev --optimize-autoloader`
4. Run `npm install && npm run build`
5. Run `php artisan migrate` (if migrations changed)
6. Clear all caches: `php artisan config:clear && view:clear && cache:clear && route:clear`
7. **Important:** If there is a new migration for `bukti_undangan`, ensure:
   - Directory `storage/app/public/bukti_undangan/` exists
   - Storage symlink `public/storage` already created
   - Jalankan `php artisan storage:link` jika perlu

---

## Deployment Checklist

Use this checklist when deploying to server (cPanel):

### Pre-Deployment (Local)

- [ ] Edit `.env` for production (`APP_ENV=production`, `APP_DEBUG=false`)
- [ ] Ensure `DAURAH_NAME` matches the event name
- [ ] Ensure `FILESYSTEM_DISK=local` (for shared hosting)
- [ ] Run `npm run build` to build frontend assets
- [ ] Commit semua perubahan ke git

### Server Setup

- [ ] Set PHP version ke ea-php82 di MultiPHP Manager
- [ ] Upload / pull project files ke server
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] Set directory permissions: `chmod -R 755 storage bootstrap/cache`
- [ ] Create storage symlink: `ln -s ../storage/app/public storage` in `public/` folder
- [ ] Create directory `storage/app/public/bukti_undangan/` with permissions 775

### Database

- [ ] Buat database dan user di MySQL Databases cPanel
- [ ] Import old database if exists (or fresh migrate)
- [ ] Jalankan `php artisan migrate --force`
- [ ] If error "column bukti_undangan already exists", skip migration or add manually:
  ```sql
  ALTER TABLE users ADD COLUMN bukti_undangan VARCHAR(255) NULL AFTER menginap;
  ```

### Post-Deployment

- [ ] `php artisan config:clear`
- [ ] `php artisan view:clear`
- [ ] `php artisan cache:clear`
- [ ] Test proof of invitation upload at `/konfirmasi`
- [ ] Test file download at `/file/bukti-undangan/[filename].pdf`
- [ ] Test admin users page: filter, sort, export CSV/Excel

### File & Folder Reference

| Path | Deskripsi |
|------|-----------|
| `storage/app/public/bukti_undangan/` | Proof of invitation file storage folder |
| `public/storage` | Symlink ke `storage/app/public/` (backward compatibility) |
| `/file/bukti-undangan/{filename}` | PHP route to serve files (primary) |
| `.env` |MUST have `MAINTENANCE_PASSWORD` and `FILESYSTEM_DISK=local` |
| `database/migrations/*bukti_undangan*.php` | Migration for bukti_undangan column |

---

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).