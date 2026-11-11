# Deployment on ghaklab

Two copies of the same GitHub repo (lab standard):

| Copy | Folder | Purpose |
|---|---|---|
| demo | `~/projects/injazat` | develop; Flock `https://injazat.test`; preview `demo injazat` -> https://demo.fahim.om |
| live | `~/live/injazat` | production containers; pull-only, never edit code here |

## First publish (once) - Claude follows the ghaklab skill "Publish an app"
1. OneBox: bucket `injazat` + bucket-scoped user (`~/server/onebox/README.md`).
2. `git clone https://github.com/gheith3/injazat ~/live/injazat`
3. `cp docker/.env.ghaklab.example docker/.env.ghaklab && chmod 600 docker/.env.ghaklab`, fill in:
   APP_URL, APP_KEY, POSTGRES_PASSWORD, REDIS_PASSWORD, S3 keys, ADMIN_EMAIL/ADMIN_PASSWORD, unique
   DB_HOST_PORT/REDIS_HOST_PORT. Save the secrets in KeyPass.
4. `docker compose -f docker/docker-compose.ghaklab.yml --env-file docker/.env.ghaklab up -d --build`
5. Check from the tunnel network: `docker run --rm --network web-tunnel-net curlimages/curl:8.10.1 -s http://injazat-app/up`
6. Public or private? -> Access app (if private), tunnel route `<host> -> http://injazat-app:80`, proxied CNAME.
7. After the first start set `RUN_MIGRATIONS=true` in docker/.env.ghaklab. Add the app to lab.fahim.om.

## Update live
```bash
# in ~/projects/injazat: branch -> PR -> CI green -> merge to main
ghaklab-deploy injazat --dry-run      # what would change, CI status
ghaklab-deploy injazat                # pull, rebuild, check http://injazat-app/up, roll back if unhealthy
```
Log: `~/.local/state/ghaklab-deploy/injazat.log`. Manual fallback:
`cd ~/live/injazat && git pull && docker compose -f docker/docker-compose.ghaklab.yml --env-file docker/.env.ghaklab up -d --build`.

AI assistants (MCP): set `ENJAZ_MCP_*` in `docker/.env.ghaklab`; Passport keys are generated once into the storage
volume unless `PASSPORT_*_KEY` are set. See [mcp.md](mcp.md).

## Rollback
`ghaklab-deploy` rolls back by itself when the health check fails. By hand:
`cd ~/live/injazat && git checkout <previous-commit>` and run the same `up -d --build`.
(Migrations are not rolled back automatically.)
