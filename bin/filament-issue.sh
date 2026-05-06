#!/usr/bin/env bash

set -e

VERSION_LABEL=${1:-"4.x"}
REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
FILAMENT_PACKAGES_PATH=${FILAMENT_PACKAGES_PATH:-"${REPO_ROOT}/packages"}
REPRO_DIR="${REPO_ROOT}/app-filament-issue-${VERSION_LABEL}"

sed_in_place() {
  if [[ "$OSTYPE" == "darwin"* ]]; then
    sed -i '' "$@"
  else
    sed -i "$@"
  fi
}

rm -rf "$REPRO_DIR"
composer create-project laravel/laravel "$REPRO_DIR"
cd "$REPRO_DIR"

cp .env.example .env
sed_in_place 's/^APP_NAME=.*/APP_NAME=Filament/' .env
php artisan key:generate

touch database/database.sqlite

composer config minimum-stability dev
composer config prefer-stable true
composer config repositories.filament-monorepo "{\"type\": \"path\", \"url\": \"${FILAMENT_PACKAGES_PATH}/*\", \"options\": {\"symlink\": false}}"

composer require filament/filament:"*" -W
php artisan filament:install --panels --no-interaction

mkdir -p app/Filament/Pages/Auth
cp "${REPO_ROOT}/bin/FilamentIssue/src/App/Filament/Pages/Auth/Login.php" app/Filament/Pages/Auth/Login.php

sed_in_place "s/->login()/->login(\\\\App\\\\Filament\\\\Pages\\\\Auth\\\\Login::class)/" app/Providers/Filament/AdminPanelProvider.php
sed_in_place "s/'email' => 'test@example.com'/'email' => 'test@filamentphp.com'/" database/seeders/DatabaseSeeder.php
sed_in_place "s|return view('welcome');|return redirect('/admin');|" routes/web.php

php artisan migrate:fresh --seed

cd "$REPO_ROOT"
OUTPUT_DIR="filament-issue"
PACKAGE_NAME="filament-issue-${VERSION_LABEL}"
rm -rf "$OUTPUT_DIR"
mkdir -p "$OUTPUT_DIR"
zip -r "$OUTPUT_DIR/$PACKAGE_NAME.zip" "app-filament-issue-${VERSION_LABEL}"
