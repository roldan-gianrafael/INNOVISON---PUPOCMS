# AWS + Spaceship Deployment Runbook (Option A)

This runbook is for the current Laravel clinic system in this repository.

Target architecture (low friction):
- 1 EC2 instance for app/web server
- 1 MySQL database:
  - cheapest: MySQL on same EC2 instance
  - better reliability: Amazon RDS MySQL
- 1 Spaceship subdomain, e.g. `clinic.yourdomain.com`

---

## 1. Pre-Deploy Checklist (Project-Specific)

Run these in project root before deployment:

```bash
php -l routes/web.php
php -l app/Http/Controllers/AdminController.php
php -l app/Http/Controllers/AppointmentController.php
php artisan test
php artisan route:list
```

What has already been hardened in this branch:
- Merged elevated role checks to `super_admin` and preserved legacy `admin` mapping.
- Added migration to convert legacy `admin` role rows to `super_admin`.
- Added schema alignment migration for missing production columns used by code.
- Removed duplicate student route declarations causing route collisions.
- Fixed MAR category update validation table name (`categories`).
- Added app timezone env support (`APP_TIMEZONE`, default `Asia/Manila`).

---

## 2. Provision AWS

## EC2
- OS: Ubuntu 22.04 LTS
- Suggested instance: `t3.small` (or `t3.micro` if traffic is light)
- Attach Elastic IP
- Security Group inbound:
  - `22` (SSH) from your office/home IP only
  - `80` (HTTP) from `0.0.0.0/0`
  - `443` (HTTPS) from `0.0.0.0/0`

## Database
- Option A1 (cheapest): local MySQL on EC2
- Option A2 (preferred): RDS MySQL (private subnet if possible), allow EC2 security group to connect to `3306`.

---

## 3. Server Setup (EC2)

```bash
sudo apt update
sudo apt install -y nginx mysql-server git unzip curl
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.1 php8.1-fpm php8.1-cli php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip php8.1-bcmath php8.1-intl
```

Install Composer:

```bash
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
php composer-setup.php
sudo mv composer.phar /usr/local/bin/composer
```

---

## 4. Deploy App Code

```bash
sudo mkdir -p /var/www
sudo chown -R $USER:$USER /var/www
cd /var/www
git clone <your-repo-url> clinic_laravel
cd clinic_laravel

composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
```

If frontend assets are used from source:

```bash
# install Node 18+ first, then:
npm ci
npm run production
```

---

## 5. Configure `.env` for Production

Minimum required:

```dotenv
APP_NAME="Clinic Laravel"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://clinic.yourdomain.com
APP_TIMEZONE=Asia/Manila

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=mysql
DB_HOST=127.0.0.1           # or RDS endpoint
DB_PORT=3306
DB_DATABASE=clinic_laravel
DB_USERNAME=clinic_user
DB_PASSWORD=strong_password

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=clinic.yourdomain.com

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
```

Important:
- Keep `APP_DEBUG=false`.
- If using HTTPS (recommended), keep `SESSION_SECURE_COOKIE=true`.

---

## 6. Run Migrations + Optimize

```bash
php artisan migrate --force
php artisan storage:link

php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Set writable permissions:

```bash
sudo chown -R www-data:www-data /var/www/clinic_laravel
sudo find /var/www/clinic_laravel/storage -type d -exec chmod 775 {} \;
sudo find /var/www/clinic_laravel/bootstrap/cache -type d -exec chmod 775 {} \;
```

---

## 7. Nginx Virtual Host

Create `/etc/nginx/sites-available/clinic_laravel`:

```nginx
server {
    listen 80;
    server_name clinic.yourdomain.com;

    root /var/www/clinic_laravel/public;
    index index.php index.html;

    access_log /var/log/nginx/clinic_access.log;
    error_log /var/log/nginx/clinic_error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

Enable and reload:

```bash
sudo ln -s /etc/nginx/sites-available/clinic_laravel /etc/nginx/sites-enabled/clinic_laravel
sudo nginx -t
sudo systemctl reload nginx
```

---

## 8. Spaceship DNS

In Spaceship DNS for your domain:
- Add `A` record
  - Host: `clinic` (or `group1`)
  - Value: `<your EC2 Elastic IP>`
  - TTL: default

Then wait for propagation (usually minutes, can be longer).

---

## 9. HTTPS

Install certbot and issue cert:

```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d clinic.yourdomain.com
```

Check auto-renew:

```bash
sudo systemctl status certbot.timer
sudo certbot renew --dry-run
```

---

## 10. Smoke Tests (Go/No-Go)

After DNS + SSL:
- Login as `super_admin` works.
- Login as `student_assistant` redirects to `/assistant/dashboard`.
- Student can:
  - register/login
  - create appointment
  - load availability slots
  - submit health form + print view
  - register/validate barcode
- Admin workspace can:
  - approve/cancel/reschedule appointments
  - complete walk-in consultation
  - open reports and MAR pages
  - view audit logs (super admin only)
  - manage student assistant accounts (super admin only)

Operational checks:
- `storage/logs/laravel.log` has no fatal errors
- uploaded images appear via `/storage/...`
- `php artisan route:list` runs without error on server

---

## 11. Rollback Plan (Minimal)

If deployment fails:
1. Keep previous release folder (or previous commit) on server.
2. Point Nginx root/symlink back to previous release.
3. Reload Nginx.
4. Restore DB backup if migration caused data issues.

At minimum, take a DB backup before running `php artisan migrate --force`.

