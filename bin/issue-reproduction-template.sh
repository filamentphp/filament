#!/usr/bin/env bash

set -e

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
FILAMENT_PACKAGES_PATH=${FILAMENT_PACKAGES_PATH:-"${REPO_ROOT}/packages"}
STUBS_DIR="${REPO_ROOT}/bin/issue-reproduction-template/stubs"
REPRO_DIR="${REPO_ROOT}/issue-reproduction-template"

# Pull the highest Laravel that this Filament checkout accepts, by reading
# Filament's own `illuminate/contracts` constraint.
LARAVEL_CONSTRAINT=$(jq -r '.require."illuminate/contracts" // empty' "${FILAMENT_PACKAGES_PATH}/support/composer.json")
if [[ -z "$LARAVEL_CONSTRAINT" ]]; then
  echo "::error::Could not extract illuminate/contracts constraint from packages/support/composer.json"
  exit 1
fi

sed_in_place() {
  if [[ "$OSTYPE" == "darwin"* ]]; then
    sed -i '' "$@"
  else
    sed -i "$@"
  fi
}

rm -rf "$REPRO_DIR"
composer create-project laravel/laravel "$REPRO_DIR" "$LARAVEL_CONSTRAINT"
cd "$REPRO_DIR"

# Set APP_NAME in `.env.example` so that the user's eventual `.env` (created by
# `composer setup` from `.env.example`) carries it. Then copy to `.env` for the
# build-time artisan commands below; the build-time `.env` is gitignored and
# never ships in the template.
sed_in_place 's/^APP_NAME=.*/APP_NAME=Filament/' .env.example
cp .env.example .env
php artisan key:generate

touch database/database.sqlite

mkdir -p packages
cp -R "${FILAMENT_PACKAGES_PATH}/." packages/

composer config minimum-stability dev
composer config prefer-stable true
composer config repositories.filament-monorepo '{"type": "path", "url": "packages/*", "options": {"symlink": false}}'

composer require filament/filament:"*" -W
php artisan filament:install --panels --no-interaction

mkdir -p app/Filament/Pages/Auth
cp "${STUBS_DIR}/Login.php" app/Filament/Pages/Auth/Login.php

sed_in_place "s/->login()/->login(\\\\App\\\\Filament\\\\Pages\\\\Auth\\\\Login::class)/" app/Providers/Filament/AdminPanelProvider.php
sed_in_place "s|return view('welcome');|return redirect('/admin');|" routes/web.php

# Replace Laravel's default README with the template's README.
cp "${STUBS_DIR}/README.md" README.md

# Inject `--seed` into the `composer setup` script's migrate step so cloning the
# template and running `composer setup` produces a working app with the test user
# already seeded.
sed_in_place 's|"@php artisan migrate --force"|"@php artisan migrate --seed --force"|' composer.json

# Clear Laravel caches that bake in build-time paths.
php artisan optimize:clear || true

# Strip dependency trees and lockfiles so `composer install` (via `composer setup`)
# resolves fresh on the user's machine against their PHP + Node versions at clone time.
rm -rf vendor node_modules
rm -f composer.lock package-lock.json

echo "✓ Built: $REPRO_DIR (Laravel constraint: $LARAVEL_CONSTRAINT)"
