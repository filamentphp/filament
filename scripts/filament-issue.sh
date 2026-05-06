#!/usr/bin/env bash

function create-app-filament-issue {
  version_label=${1:-"4.x"}
  filament_packages_path=${FILAMENT_PACKAGES_PATH:-"$(pwd)/packages"}
  repro_dir="app-filament-issue-${version_label}"

  rm -rf "$repro_dir"
  composer create-project laravel/laravel "$repro_dir" --no-install
  cd "$repro_dir"

  composer config minimum-stability dev
  composer config prefer-stable true
  composer config repositories.filament-monorepo "{\"type\": \"path\", \"url\": \"${filament_packages_path}/*\", \"options\": {\"symlink\": false}}"

  composer install
  composer require filament/filament:"*" -W

  cp .env.example .env
  if [[ "$OSTYPE" == "darwin"* ]]; then
    sed -i '' 's/^APP_NAME=.*/APP_NAME=Filament/' .env
  else
    sed -i 's/^APP_NAME=.*/APP_NAME=Filament/' .env
  fi
  php artisan key:generate

  touch database/database.sqlite

  php artisan filament:install --panels --no-interaction

  install_auto_login
  add_root_redirect_to_admin_panel

  php artisan migrate:fresh --seed

  cd -
  package_zip_file "filament-issue-${version_label}"
}

function install_auto_login {
  mkdir -p app/Filament/Pages/Auth

  cp "$(dirname "$0")/../stubs/Filament/Login.php" app/Filament/Pages/Auth/Login.php

  if [[ "$OSTYPE" == "darwin"* ]]; then
    sed -i '' "s/->login()/->login(\\\\App\\\\Filament\\\\Pages\\\\Auth\\\\Login::class)/" app/Providers/Filament/AdminPanelProvider.php
    sed -i '' "s/'email' => 'test@example.com'/'email' => 'test@filamentphp.com'/" database/seeders/DatabaseSeeder.php
  else
    sed -i "s/->login()/->login(\\\\App\\\\Filament\\\\Pages\\\\Auth\\\\Login::class)/" app/Providers/Filament/AdminPanelProvider.php
    sed -i "s/'email' => 'test@example.com'/'email' => 'test@filamentphp.com'/" database/seeders/DatabaseSeeder.php
  fi

  echo "Auto-login functionality installed successfully."
}

function add_root_redirect_to_admin_panel {
  if [[ "$OSTYPE" == "darwin"* ]]; then
    sed -i '' "s|return view('welcome');|return redirect('/admin');|" routes/web.php
  else
    sed -i "s|return view('welcome');|return redirect('/admin');|" routes/web.php
  fi
}

function package_zip_file {
  package_name=$1
  output_dir="filament-issue"

  rm -rf "$output_dir"
  mkdir -p "$output_dir"

  zip -r "$output_dir/$package_name.zip" "app-$package_name"
}

function prepare_sandbox {
  package_name=$1
  sandbox_dir=filament-issue-sandbox

  rm -rf temp
  rm -rf "$sandbox_dir"
  mkdir -p "$sandbox_dir"

  unzip "filament-issue/$package_name.zip" -d temp
  mv "temp/app-$package_name/"* "$sandbox_dir/"
  mv "temp/app-$package_name/".* "$sandbox_dir/" 2>/dev/null || true
}

function test_filament_issue {
  version_label=${1:-"4.x"}
  prepare_sandbox "filament-issue-${version_label}"
  cd filament-issue-sandbox && php artisan serve
}
