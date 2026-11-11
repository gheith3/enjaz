# injazat - rules for AI agents (Claude, Codex)

Project created from `gheith3/ghaklab-template-laravel`. Lab conventions: ghaklab skill / `~/projects/CLAUDE.md`.

- Laravel app is in `backend/` (its own Boost guidelines: `backend/CLAUDE.md`, `backend/AGENTS.md`).
  Run artisan/composer/npm from `backend/`. PHP 8.4 (Flock: `flock isolate php 8.4`).
- Stack is fixed unless the user decides otherwise: PostgreSQL, Redis (cache/session/queue + Horizon),
  Filament for admin UI, files on OneBox S3 (`FILESYSTEM_DISK=s3`, path-style). Never local-disk uploads,
  never MinIO/RustFS in this project, never the OneBox root keys.
- Two copies: demo/dev = `~/projects/injazat` (Flock `injazat.test`, previewed at demo.fahim.om, bucket
  `injazat-demo`), live = `~/live/injazat` (docker/docker-compose.ghaklab.yml, bucket `injazat`). Never edit code
  in the live copy. Deploy steps: `docs/deployment.md`.
- Secrets only in git-ignored files: `backend/.env`, `docker/.env.ghaklab`, `docker/.env.local`.
- Keep `docker/` and `backend/docker/` working: if you add a PHP extension, a queue, a scheduled task or an
  env variable, update the Dockerfile / supervisord.conf / compose env / `.env*.example` in the same change.
- Before committing: `vendor/bin/pint --dirty`, `php artisan test --parallel --compact`, `npm run build`.
  GitHub CI runs the same on every PR and push to main (`.github/workflows/ci.yml`); merge only when green.
- UI changes: check them with `lab-shot https://injazat.test --both --all` and look at the screenshots.
  Light/dark theme: `<x-theme-script />` in `<head>` + `<x-theme-toggle />`; use `dark:` classes.
- Deploy live with `ghaklab-deploy injazat` (see docs/deployment.md), never by editing ~/live.

## Injazat rebuild (read before any change)
- Plan: `docs/architecture.md`. How-to: `docs/development.md`. The static design in `/design` is the visual
  reference only: never copy a page into a view; rebuild each section as a component fed by models.
- Every piece of content is editable in `/admin`; UI labels in `backend/lang/{ar,en}/site.php`.
- Responsive exactly as the design (same CSS files in `backend/resources/site/css`, same breakpoints, `vp-lock`).
- After adding resources or page sections: `php artisan site:sync` (also runs on every container start).
- Tests are PHPUnit (`tests/Feature/...`); every public route and dashboard screen has one.
