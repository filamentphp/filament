<?php

namespace Filament\Notifications;

use Closure;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RuntimeException;

use function Filament\Support\encode_key_segments;

class NotificationMailbox
{
    protected const MAX_PENDING_NOTIFICATIONS = 50;

    /** @var array<array<string, mixed>> */
    protected array $pendingNotifications = [];

    /** @param array<string, mixed> $notification */
    public function push(array $notification): void
    {
        $this->initializeScope();

        $this->pendingNotifications[] = $notification;

        if (count($this->pendingNotifications) > static::MAX_PENDING_NOTIFICATIONS) {
            array_shift($this->pendingNotifications);
        }
    }

    public function initializeScope(): void
    {
        if (! session()->has('_token')) {
            session()->regenerateToken();
        }

        // Existing session entropy makes concurrent initialization deterministic,
        // without allowing a revoked cookie to reconstruct the mailbox address.
        if (! session()->has('filament.notifications.mailbox')) {
            $this->pendingNotifications = [];

            session()->put('filament.notifications.mailbox', hash_hmac(
                'sha256',
                encode_key_segments(session()->getId(), session()->token()),
                config('app.key'),
            ));
        }
    }

    public function rotateScope(bool $shouldPreservePublishedNotifications = false): void
    {
        $this->initializeScope();

        if ($shouldPreservePublishedNotifications) {
            $this->pendingNotifications = array_slice([...$this->pull(), ...$this->pendingNotifications], -static::MAX_PENDING_NOTIFICATIONS);
        }

        session()->put('filament.notifications.mailbox', Str::random(40));
    }

    public function hasPendingNotifications(): bool
    {
        return count($this->pendingNotifications) > 0;
    }

    public function hasPublishedNotifications(): bool
    {
        return $this->mutate(static fn (Repository $cache, string $key): bool => $cache->has($key));
    }

    public function flush(): void
    {
        if (! $this->hasPendingNotifications()) {
            return;
        }

        $this->mutate(function (Repository $cache, string $key): void {
            if (! $cache->put(
                $key,
                array_slice([...($cache->get($key) ?? []), ...$this->pendingNotifications], -static::MAX_PENDING_NOTIFICATIONS),
                now()->addMinutes(max(1, (int) config('session.lifetime', 120))),
            )) {
                throw new RuntimeException('Could not store Filament notifications in the mailbox.');
            }
        });

        $this->pendingNotifications = [];
    }

    /** @return array<array<string, mixed>> */
    public function pull(): array
    {
        return $this->mutate(function (Repository $cache, string $key): array {
            $notifications = $cache->get($key) ?? [];

            if (count($notifications) && ! $cache->forget($key)) {
                throw new RuntimeException('Could not remove Filament notifications from the mailbox.');
            }

            return $notifications;
        });
    }

    protected function getScope(): string
    {
        $this->initializeScope();

        return session()->get('filament.notifications.mailbox');
    }

    protected function mutate(Closure $callback): mixed
    {
        $cache = Cache::store(config('filament.cache_store'));
        $store = $cache->getStore();
        $key = 'filament:notifications:' . $this->getScope();

        if (! $store instanceof LockProvider) {
            return $callback($cache, $key);
        }

        // Only cache operations run under this lock, never application work,
        // rendering, or waiting for the browser to receive the response.
        return $store->lock("{$key}:lock", 10)->block(5, fn (): mixed => $callback($cache, $key));
    }
}
