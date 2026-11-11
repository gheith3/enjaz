# Deployment

| Copy | Where | Purpose |
|---|---|---|
| dev | `~/projects/injazat` on ghaklab | develop; Flock `https://injazat.test`; preview `demo injazat` -> https://demo.fahim.om; bucket `injazat-demo` |
| production | https://injazat.om (owner's server) | Portainer stack from the GitHub repo gheith3/enjaz |

## Production: https://injazat.om (owner's server, Portainer)

The repo is **gheith3/enjaz**. The server runs a Portainer git stack (repo main, file `docker/docker-compose.prod.yml`)
with the image `ghcr.io/gheith3/enjaz:latest`. Portainer refuses repos that contain symlinks: never commit one.
The stack runs `docker/docker-compose.prod.yml` with the image `ghcr.io/gheith3/enjaz:latest`.

Release:
```bash
# branch -> PR -> CI green -> merge to main, then:
git checkout main && git pull
git tag -a v2.0.3 -m "..." && git push origin v2.0.3   # CI -> image ghcr.io/gheith3/enjaz:{2.0.3,2.0,latest,<sha>} -> Portainer webhook
```
`.github/workflows/build-and-push.yml` builds `backend/` with the `design/` folder baked in (`/var/www/design`), then
calls the `PORTAINER_WEBHOOK_URL` secret of the repo (Portainer pulls the new image and restarts the stack). On start
the entrypoint migrates, runs `site:sync` (roles, pages, sections, super admin = `ADMIN_EMAIL`) and caches config.

Stack settings: `docker/.env.example` (REQUIRED: APP_URL, APP_KEY, POSTGRES_PASSWORD, AWS keys, S3_URL). Container names
stay `enjaz-*` (the server's nginx proxies to `enjaz-app:80` on the `proxy` network); the volumes are `injazat_*`.

First start on an empty database (Portainer → container `enjaz-app` → Console, user `www-data`):
```bash
php artisan injazat:import-design          # content of the design (takes a few minutes; images go to S3)
php artisan responsecache:clear
```
Then create the first user's password: `php artisan make:filament-user` (with the ADMIN_EMAIL address), or log in if it
exists. Sample content of the design (news, articles, writers) is imported as drafts.

AI assistants (MCP): set `ENJAZ_MCP_*` in the stack settings; Passport keys are generated once into the storage volume
unless `PASSPORT_*_KEY` are set. See [mcp.md](mcp.md).

If the site does not change after a release: in Portainer use **Pull and redeploy** (re-pull the image), and check
that `IMAGE_TAG` in the stack is `latest` (or the new version).

## Rollback
Set `IMAGE_TAG` in the stack to the previous version (e.g. `2.0.1`) and redeploy. Migrations are not rolled back.
