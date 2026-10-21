---
title: Contributing
---

## Introduction

You can run Filament's test suite from a checkout of this repository after installing its Composer and npm dependencies and building the JavaScript assets:

```bash
composer install
npm ci
npm run build:js
composer test
```

`composer test` runs the parallel SQLite tests, serial SQLite tests, and PHPStan. Browser tests also require Chromium, which you can install using `npx playwright install --with-deps chromium`.

## Testing with other databases

Copy `.env.testing.example` to `.env.testing` to configure your local database connections. Use database-specific variables so credentials for one engine do not affect another.

<Aside variant="danger">
    Use disposable test databases only, never development or production data. Tests run migrations and delete data. Your test account must also be able to create and drop the databases used by parallel workers.
</Aside>

Create a `testing` database before running MySQL or PostgreSQL tests. The SQL Server connection creates its test database automatically. CI uses MySQL 8.0, PostgreSQL 15, and SQL Server 2022 Developer. Run both the parallel and serial commands for the engine you are testing:

```bash
composer test:mysql
composer test:serial:mysql
composer test:pgsql
composer test:serial:pgsql
composer test:sqlsrv
composer test:serial:sqlsrv
```

`composer test:all-databases` runs the parallel suite against SQLite, MySQL, PostgreSQL, and SQL Server. Run each engine's `test:serial:*` command separately for serial coverage.

### Configuring SQL Server

Install Microsoft's [PHP drivers for SQL Server](https://learn.microsoft.com/en-us/sql/connect/php/download-drivers-php-sql-server), enabling `pdo_sqlsrv` for your PHP version, and the [Microsoft ODBC Driver for SQL Server](https://learn.microsoft.com/en-us/sql/connect/odbc/download-odbc-driver-for-sql-server). Verify that `php --ri pdo_sqlsrv` succeeds.

CI uses the official `mcr.microsoft.com/mssql/server:2022-latest` image with `MSSQL_PID=Developer`, `ACCEPT_EULA=Y`, and `MSSQL_MEMORY_LIMIT_MB=2048`, publishing port `1433` on loopback only. For a matching disposable local service, set `MSSQL_SA_PASSWORD=Filament_Test123!`. This password is for testing only.

Configure `.env.testing` for that service:

```dotenv
DB_HOST=127.0.0.1
SQLSRV_PORT=1433
SQLSRV_USERNAME=sa
SQLSRV_PASSWORD=Filament_Test123!
SQLSRV_ENCRYPT=yes
SQLSRV_TRUST_SERVER_CERTIFICATE=true
```

`SQLSRV_PORT`, `SQLSRV_USERNAME`, and `SQLSRV_PASSWORD` fall back to `DB_PORT`, `DB_USERNAME`, and `DB_PASSWORD`, respectively, with defaults of `1433`, `sa`, and an empty password. Encryption defaults to `yes`, and trusting the server certificate defaults to `true` for the local self-signed test service. Do not use this certificate trust setting for production connections.

Before running tests, use `/opt/mssql-tools18/bin/sqlcmd` inside the container with `-S localhost -U sa -C -b -Q "SELECT 1"` to verify query readiness. Supply the test password through `SQLCMDPASSWORD`; `-C` trusts the local service's self-signed certificate.

### Running SQL Server in an Amp orb

The orb setup installs the PHP and ODBC drivers and caches the SQL Server image. Run `amp orb services ensure` to start the supervised Docker daemon and SQL Server service. SQL Server listens only on loopback port `1433`, without a public portal. Its disposable databases persist in the `filament-sqlserver` Docker volume across service restarts and orb pause/resume.

The setup adds the local test password to `.env.testing` if `SQLSRV_PASSWORD` is not already configured, so you can run `composer test:sqlsrv` and `composer test:serial:sqlsrv` without exporting credentials. It does not overwrite existing credentials.
