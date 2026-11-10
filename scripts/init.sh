#!/usr/bin/env bash
# Rename the template placeholder `myapp` to the real project name. Run ONCE right after creating
# the repo from the template (ghaklab-new does this for you):   scripts/init.sh <name>
# <name>: lowercase letters, digits and dashes (e.g. mylab, my-shop). DB names use underscores.
set -euo pipefail
cd "$(dirname "$0")/.."
name="${1:?usage: scripts/init.sh <name>}"
[[ "$name" =~ ^[a-z][a-z0-9-]{1,30}$ ]] || { echo "invalid name: $name" >&2; exit 1; }
grep -q 'myapp' README.md || { echo "already initialised (no 'myapp' left)"; exit 0; }
db="${name//-/_}"
files=$(grep -rlI 'myapp' --exclude-dir=vendor --exclude-dir=node_modules --exclude-dir=.git --exclude=init.sh . || true)
for f in $files; do
  sed -i -e "s/myapp_user/${db}_user/g" -e "s/myapp_local/${db}_local/g" \
         -e "s/POSTGRES_DB=myapp\$/POSTGRES_DB=${db}/" -e "s/POSTGRES_DB:-myapp}/POSTGRES_DB:-${db}}/g" \
         -e "s/DB_DATABASE=myapp\$/DB_DATABASE=${db}/" -e "s/POSTGRES_DB: myapp\$/POSTGRES_DB: ${db}/" \
         -e "s/DB_DATABASE: myapp\$/DB_DATABASE: ${db}/" -e "s/-d myapp\"/-d ${db}\"/" \
         -e "s/myapp/${name}/g" "$f"
done
sed -i "s#\"name\": \"laravel/laravel\"#\"name\": \"gheith3/${name}\"#" backend/composer.json
# The package rename changes composer.json's hash: refresh composer.lock's hash only (no version changes).
if command -v composer >/dev/null 2>&1; then
  (cd backend && composer update --lock --no-install --no-scripts -q) || echo "WARNING: run 'composer update --lock' in backend/" >&2
fi
echo "initialised as '$name' (database name '$db'). Changed:"; echo "$files" | sed 's/^/  /'
