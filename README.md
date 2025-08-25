# KaajAsse — Task Management (PHP)

A small monolithic task/kanban app written in procedural PHP with MySQL and vanilla JS.

Summary
- Entry point: `index.php` (redirects to login or dashboard)
- DB: `conf/database/db_connect.php` (uses vlucas/phpdotenv)
- Main pages: `src/*` (login, dashboard, kanban, profile, projects)

## Quick start (local)
1. Install PHP (8.x recommended) and Composer.
2. Clone and install deps:

    ```powershell
    git clone https://github.com/combo666/KaajAsse.git
    cd KaajAsse
    composer install
    ```

3. Create `.env` at project root with DB values (example):

    ```env
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_USER=root
    DB_PASS=yourpassword
    DB_NAME=KaajAsse
    ```

4. Import schema and run a quick dev server:

    ```powershell
    mysql -u root -p < conf/kaajAsse.sql
    php -S localhost:8000
    ```

Open [http://localhost:8000](http://localhost:8000)

## What's been improved (security)
- Passwords moved from `md5()` to `password_hash()` / `password_verify()` with a fallback to re-hash legacy md5 accounts on next login.
- Login and registration now use prepared statements.

## Recommended next work
- Migrate remaining `mysqli_query()` usages to prepared statements (see `SECURITY_INVENTORY.md`).
- Escape user-supplied output with `htmlspecialchars()` to prevent XSS.
- Add CSRF tokens to forms and AJAX endpoints.
- Add role/ownership checks on destructive endpoints (delete/update task).
- Add simple automated tests and a static analyzer (PHPStan/ Psalm).

If you want, I can continue and automatically apply the next high-priority fixes (e.g., migrate `src/board/kanban_board.php`).

## Docker (build and run)

This repository includes a production-ready `Dockerfile` and a `docker-compose.yml` to run the app with PHP-FPM, Nginx and MySQL.

1) Create a Docker environment file (example `.env.docker`) in the project root with the DB variables referenced by `docker-compose.yml`:

```env
MYSQL_ROOT_PASSWORD=your_root_password
MYSQL_DATABASE=KaajAsse
MYSQL_USER=kaaj_user
MYSQL_PASSWORD=strongpassword
```

2) Build and start the stack:

```powershell
# Build images and start containers
docker compose up --build -d
```

3) Import the schema (once DB is ready):

```powershell
# Use containerized mysql client
docker exec -i kaajasse_db mysql -u root -p$env:MYSQL_ROOT_PASSWORD $env:MYSQL_DATABASE < conf/kaajAsse.sql
```

4) Open the site: http://localhost

Notes and deployment tips
- The `Dockerfile` installs PHP extensions (mysqli, pdo_mysql) and Composer. It copies the app into `/var/www/html` and runs Composer at build time.
- For production, prefer building an image in CI and pushing to a registry, then deploying the image on your host or orchestrator (Kubernetes, ECS, etc.).
- Make sure `.env` or any secrets are set via environment variables in your orchestration platform and not committed to the repo.

If you'd like, I can also create a small CI config (GitHub Actions) to build and push the image on merge.


