---
title: Optimizing local development
---
import Aside from "@components/Aside.astro"

This section includes optional tips to optimize performance when running your Filament app locally.

If you're looking for production-specific optimizations, check out [Deploying to production](../deployment).

## Enabling OPcache

[OPcache](https://www.php.net/manual/en/book.opcache.php) improves PHP performance by storing precompiled script bytecode in shared memory, thereby removing the need for PHP to load and parse scripts on each request. This can significantly speed up your local development environment, especially for larger applications.

### Checking OPcache status

To check if [OPcache](https://www.php.net/manual/en/book.opcache.php) is enabled, run:

```bash
php -r "echo 'opcache.enable => ' . ini_get('opcache.enable') . PHP_EOL;"
```

You should see `opcache.enable => 1`. If not, enable it by adding the following line to your `php.ini`:

```ini
opcache.enable=1 # Enable OPcache
```

<Aside variant="tip">
    To locate your `php.ini` file, run: `php --ini`
</Aside>

### Configuring OPcache settings

If you're experiencing slow response times or suspect that OPcache is running out of space, you can adjust these parameters in your `php.ini` file:

```ini
opcache.memory_consumption=128
opcache.max_accelerated_files=10000
```

<Aside variant="tip">
    To locate your `php.ini` file, run: `php --ini`
</Aside>

- `opcache.memory_consumption`: defines how much memory (in megabytes) OPcache can use to store precompiled PHP code. You can try setting this to `128` and adjust based on your project's needs.
- `opcache.max_accelerated_files`: sets the maximum number of PHP files that OPcache can cache. You can try `10000` as a starting point and increase if your application includes a large number of files.

These settings are optional but useful if you're troubleshooting performance or working on a large Laravel app.

## Exclude your project folder from antivirus scanning

Issues with the performance of Filament, particularly on Windows, often involve [Microsoft Defender](https://www.microsoft.com/en-us/microsoft-365/microsoft-defender-for-individuals).

Security software, such as realtime file scanners or antivirus tools, can slow down your development environment by scanning files every time they're accessed. This can affect PHP execution, view rendering, and performance in general.

If you're noticing slowness, consider excluding your local project folder from realtime scanning.

Tools like [Microsoft Defender](https://www.microsoft.com/en-us/microsoft-365/microsoft-defender-for-individuals), or similar antivirus solutions, can be configured to skip specific directories. Check your antivirus or security software documentation for instructions on how to exclude specific folders from realtime scanning.

<Aside variant="warning">
    Only exclude folders from scanning if you fully trust the project and understand the risks.
</Aside>

## Disabling debugging tools

Debugging tools can be very useful for local development, but they can significantly slow down your application when you aren't actively using them. Temporarily disabling these tools when you need maximum performance can make a noticeable difference in your development experience.

### Disabling view debugging in Laravel Herd

[Laravel Herd](https://herd.laravel.com/) includes a view debugging tool for [macOS](https://herd.laravel.com/docs/macos/debugging/dumps#views) and [Windows](https://herd.laravel.com/docs/windows/debugging/dumps#views). It shows which views were rendered and what data was passed to them during a request.

While helpful for debugging, this feature can significantly slow down your app. If you're not actively using it, it's best to turn it off.

To disable view debugging in Herd:

- Open Herd > Dumps.
- Click Settings.
- Uncheck the "Views" option.

### Disabling Debugbar

While useful for debugging, [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) can slow down your application, especially on complex pages, because it collects and renders a large amount of data on each request.

If you're noticing slowness, try disabling it by adding the following line to your `.env` file:

```dotenv
DEBUGBAR_ENABLED=false
```

If you still need Debugbar for development, consider disabling specific collectors you're not using.
Refer to the [Debugbar documentation](https://github.com/barryvdh/laravel-debugbar?tab=readme-ov-file#debugbar-for-laravel) for details.

### Disabling Xdebug

[Xdebug](https://xdebug.org) is a powerful tool for debugging, but it can significantly slow down performance. If you notice performance issues, check if `Xdebug` is installed and consider disabling it.

If `Xdebug` is installed but not disabled, it will still be enabled by default. If you have it installed, make sure it is explicitly disabled in your `php.ini` file:

```ini
xdebug.mode=off # Disable Xdebug
```

<Aside variant="tip">
    To locate your `php.ini` file, run: `php --ini`
</Aside>

## Caching Blade icons

Caching [Blade icons](https://blade-ui-kit.com/blade-icons) can help improve performance during local development, especially in views that render many icons.

To enable caching, run:

```bash
php artisan icons:cache
```

Make sure that when you install new Blade icon packages, you run the command again to discover the new icons.
