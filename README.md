# injazat

Laravel project on the **ghaklab** stack (from the `ghaklab-template-laravel` template).

| | |
|---|---|
| Backend | Laravel 13, PHP 8.4, Filament 5 (admin panel at `/admin`), Horizon (`/horizon`) |
| Data | PostgreSQL 16, Redis 7 (cache, sessions, queues) |
| Files | OneBox S3 (`https://onebox.fahim.om`): bucket `injazat-demo` (demo), `injazat` (live) |
| Image | `backend/Dockerfile`: nginx + php-fpm + Horizon + scheduler under supervisor |

## Layout
```
backend/   Laravel app (+ Dockerfile and docker/ for the production image)
docs/      project documents (start with docs/README.md)
docker/    docker-compose.local.yml  - full stack in Docker on any machine
scripts/   init.sh (rename the template, run once)
```

## Dev and production
| Copy | Where | URL |
|---|---|---|
| dev | `~/projects/injazat` on ghaklab (Flock site `injazat.test`) | https://demo.fahim.om (`demo injazat`) |
| production | owner's server, Portainer stack `docker/docker-compose.prod.yml` | https://injazat.om |

Work on a branch, PR to gheith3/enjaz, merge when CI is green, then tag `v*` to release. See `docs/deployment.md`.

## Local dev (Flock, on ghaklab)
```bash
cd backend
cp .env.example .env            # DB password + OneBox demo keys (ghaklab-new fills these)
composer install && php artisan key:generate
php artisan migrate --seed      # admin@example.com / password (local only)
npm ci && npm run build
flock link injazat && flock secure injazat && flock isolate php 8.4
demo injazat                      # show it at https://demo.fahim.om
```

## Local dev (Docker, any machine)
```bash
cp docker/.env.local.example docker/.env.local   # OneBox demo keys
docker compose -f docker/docker-compose.local.yml --env-file docker/.env.local up -d --build
# http://localhost:8000/admin
```
