# Filament issue reproduction template

A minimal Laravel + Filament application for reproducing bugs you would like to file at [`filamentphp/filament`](https://github.com/filamentphp/filament/issues).

This repository is regenerated automatically from each stable Filament release. The branch you cloned matches the Filament major version it was built against:

- `3.x` — latest Filament v3 release (maintenance mode)
- `4.x` — latest Filament v4 release
- `5.x` — latest Filament v5 release

## Setup

After cloning:

```bash
composer setup
php artisan serve
```

`composer setup` installs Composer + npm dependencies, copies `.env.example` to `.env`, generates an app key, migrates and seeds the database, and builds frontend assets.

Log in at <http://127.0.0.1:8000/admin> with `test@example.com` / `password`.

Add the minimum code needed to reproduce your bug, push your repository, and link it from your Filament issue.
