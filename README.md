# myapp

Laravel project on the **ghaklab** stack (from the `ghaklab-template-laravel` template).

| | |
|---|---|
| Backend | Laravel 13, PHP 8.4, Filament 5 (admin panel at `/admin`), Horizon (`/horizon`) |
| Data | PostgreSQL 16, Redis 7 (cache, sessions, queues) |
| Files | OneBox S3 (`https://onebox.fahim.om`): bucket `myapp-demo` (demo), `myapp` (live) |
| Image | `backend/Dockerfile`: nginx + php-fpm + Horizon + scheduler under supervisor |

## Layout
```
backend/   Laravel app (+ Dockerfile and docker/ for the production image)
docs/      project documents (start with docs/README.md)
docker/    docker-compose.local.yml  - full stack in Docker on any machine
           docker-compose.ghaklab.yml - LIVE on ghaklab behind the Cloudflare Tunnel
scripts/   init.sh (rename the template, run once)
```

## Two copies on ghaklab
| Copy | Folder | Runs as | URL |
|---|---|---|---|
| demo (dev) | `~/projects/myapp` | Flock site `myapp.test` | https://demo.fahim.om (`demo myapp`) |
| live | `~/live/myapp` | `docker/docker-compose.ghaklab.yml` | e.g. https://myapp.fahim.om |

Work, commit and push in the demo copy; the live copy only pulls and rebuilds. See `docs/deployment.md`.

## Local dev (Flock, on ghaklab)
```bash
cd backend
cp .env.example .env            # DB password + OneBox demo keys (ghaklab-new fills these)
composer install && php artisan key:generate
php artisan migrate --seed      # admin@example.com / password (local only)
npm ci && npm run build
flock link myapp && flock secure myapp && flock isolate php 8.4
demo myapp                      # show it at https://demo.fahim.om
```

## Local dev (Docker, any machine)
```bash
cp docker/.env.local.example docker/.env.local   # OneBox demo keys
docker compose -f docker/docker-compose.local.yml --env-file docker/.env.local up -d --build
# http://localhost:8000/admin
```
