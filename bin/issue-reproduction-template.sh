#!/usr/bin/env bash

set -e

FILAMENT_BRANCH=${1:?"Usage: $0 <filament_branch> (e.g. 4.x)"}
if [[ ! "$FILAMENT_BRANCH" =~ ^([0-9]+)\.x$ ]]; then
  echo "::error::Branch '$FILAMENT_BRANCH' is not in the form '<major>.x'"
  exit 1
fi
FILAMENT_MAJOR="${BASH_REMATCH[1]}"
FILAMENT_CONSTRAINT="^${FILAMENT_MAJOR}.0"

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
FILAMENT_PACKAGES_PATH=${FILAMENT_PACKAGES_PATH:-"${REPO_ROOT}/packages"}
STUBS_DIR="${REPO_ROOT}/bin/issue-reproduction-template/stubs"
REPRO_DIR="${REPO_ROOT}/issue-reproduction-template"

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

# Set on `.env.example` first so the user's eventual `cp .env.example .env`
# (run by `composer setup`) inherits the customisation.
sed_in_place 's/^APP_NAME=.*/APP_NAME=Filament/' .env.example
cp .env.example .env
php artisan key:generate

touch database/database.sqlite

composer require filament/filament:"$FILAMENT_CONSTRAINT" -W
php artisan filament:install --panels --no-interaction

mkdir -p app/Filament/Pages/Auth
cp "${STUBS_DIR}/Login.php" app/Filament/Pages/Auth/Login.php

sed_in_place "s/->login()/->login(\\\\App\\\\Filament\\\\Pages\\\\Auth\\\\Login::class)/" app/Providers/Filament/AdminPanelProvider.php
sed_in_place "s|return view('welcome');|return redirect('/admin');|" routes/web.php

cp "${STUBS_DIR}/README.md" README.md

sed_in_place 's|"@php artisan migrate --force"|"@php artisan migrate --seed --force"|' composer.json

# Drop build-time path bakings before stripping vendor.
php artisan optimize:clear || true

# Ship without vendor + lockfiles so `composer install` (run by `composer setup`)
# re-resolves on the user's machine at clone time.
rm -rf vendor node_modules
rm -f composer.lock package-lock.json

echo "✓ Built: $REPRO_DIR (Filament ${FILAMENT_CONSTRAINT}, Laravel ${LARAVEL_CONSTRAINT})"
