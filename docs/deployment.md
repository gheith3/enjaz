# Deployment on ghaklab

Two copies of the same GitHub repo (lab standard):

| Copy | Folder | Purpose |
|---|---|---|
| demo | `~/projects/myapp` | develop; Flock `https://myapp.test`; preview `demo myapp` -> https://demo.fahim.om |
| live | `~/live/myapp` | production containers; pull-only, never edit code here |

## First publish (once) - Claude follows the ghaklab skill "Publish an app"
1. OneBox: bucket `myapp` + bucket-scoped user (`~/server/onebox/README.md`).
2. `git clone https://github.com/gheith3/myapp ~/live/myapp`
3. `cp docker/.env.ghaklab.example docker/.env.ghaklab && chmod 600 docker/.env.ghaklab`, fill in:
   APP_URL, APP_KEY, POSTGRES_PASSWORD, REDIS_PASSWORD, S3 keys, ADMIN_EMAIL/ADMIN_PASSWORD, unique
   DB_HOST_PORT/REDIS_HOST_PORT. Save the secrets in KeyPass.
4. `docker compose -f docker/docker-compose.ghaklab.yml --env-file docker/.env.ghaklab up -d --build`
5. Check from the tunnel network: `docker run --rm --network web-tunnel-net curlimages/curl:8.10.1 -s http://myapp-app/up`
6. Public or private? -> Access app (if private), tunnel route `<host> -> http://myapp-app:80`, proxied CNAME.
7. After the first start set `RUN_MIGRATIONS=true` in docker/.env.ghaklab. Add the app to lab.fahim.om.

## Update live
```bash
# in ~/projects/myapp: branch -> PR -> CI green -> merge to main
ghaklab-deploy myapp --dry-run      # what would change, CI status
ghaklab-deploy myapp                # pull, rebuild, check http://myapp-app/up, roll back if unhealthy
```
Log: `~/.local/state/ghaklab-deploy/myapp.log`. Manual fallback:
`cd ~/live/myapp && git pull && docker compose -f docker/docker-compose.ghaklab.yml --env-file docker/.env.ghaklab up -d --build`.

## Rollback
`ghaklab-deploy` rolls back by itself when the health check fails. By hand:
`cd ~/live/myapp && git checkout <previous-commit>` and run the same `up -d --build`.
(Migrations are not rolled back automatically.)
