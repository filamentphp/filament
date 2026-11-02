<?php

namespace Filament\Notifications;

use Closure;
use Exception;
use Illuminate\Cache\FileStore;
use Illuminate\Contracts\Cache\LockProvider;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use RuntimeException;

use function Filament\Support\encode_key_segments;

class NotificationMailbox
{
    protected const MAX_PENDING_NOTIFICATIONS = 50;

    /** @var array<array{notification: string | array<string, mixed>, batch?: ?string}> */
    protected array $pendingNotifications = [];

    /** @param array<string, mixed> $notification */
    public function push(array $notification): void
    {
        $this->initializeScope();

        $this->pendingNotifications[] = ['notification' => serialize($notification)];

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
            $this->pendingNotifications = array_slice([...$this->pullEntries(), ...$this->pendingNotifications], -static::MAX_PENDING_NOTIFICATIONS);
        }

        session()->put('filament.notifications.mailbox', Str::random(40));
    }

    public function hasPendingNotifications(): bool
    {
        return count($this->pendingNotifications) > 0;
    }

    public function hasPublishedNotifications(): bool
    {
        $cache = Cache::store(config('filament.cache_store'));
        $hasUnownedNotifications = static fn (Repository $cache, string $key): bool => collect($cache->get($key) ?? [])
            ->contains(static fn (array $entry): bool => ! isset($entry['batch']));

        // `FileStore::get()` can delete expired entries after reading them, so an
        // unlocked read could delete notifications published in the meantime.
        if ($cache->getStore() instanceof FileStore) {
            return $this->mutate($hasUnownedNotifications);
        }

        return $hasUnownedNotifications($cache, $this->getCacheKey());
    }

    public function flush(?string $batch = null): void
    {
        if (! $this->hasPendingNotifications()) {
            return;
        }

        $this->mutate(function (Repository $cache, string $key) use ($batch): void {
            if (! $cache->put(
                $key,
                array_slice([
                    ...($cache->get($key) ?? []),
                    ...array_map(
                        static fn (array $entry): array => $entry + ['batch' => $batch],
                        $this->pendingNotifications,
                    ),
                ], -static::MAX_PENDING_NOTIFICATIONS),
                now()->addMinutes(max(1, (int) config('session.lifetime', 120))),
            )) {
                throw new RuntimeException('Could not store Filament notifications in the mailbox.');
            }
        });

        $this->pendingNotifications = [];
    }

    /**
     * @param  array<string>  $batches
     * @return array<array<string, mixed>>
     */
    public function pull(array $batches = []): array
    {
        return $this->decodeEntries($this->pullEntries($batches));
    }

    /** @return ?array<array<string, mixed>> */
    public function tryPull(): ?array
    {
        try {
            $entries = $this->withLock(
                fn (Repository $cache, string $key): array => $this->consumeEntries($cache, $key, []),
                shouldWaitForLock: false,
            );
        } catch (LockTimeoutException) {
            return null;
        } catch (Exception $exception) {
            report($exception);

            return null;
        }

        return $this->decodeEntries($entries);
    }

    /**
     * @param  array<array{notification: string | array<string, mixed>, batch: ?string}>  $entries
     * @return array<array<string, mixed>>
     */
    protected function decodeEntries(array $entries): array
    {
        return array_map(
            static fn (array $entry): array => is_string($entry['notification'])
                ? unserialize($entry['notification'], ['allowed_classes' => [HtmlString::class]])
                : $entry['notification'],
            $entries,
        );
    }

    /**
     * @param  ?array<string>  $batches
     * @return array<array{notification: string | array<string, mixed>, batch: ?string}>
     */
    protected function pullEntries(?array $batches = null): array
    {
        return $this->mutate(fn (Repository $cache, string $key): array => $this->consumeEntries($cache, $key, $batches));
    }

    /**
     * @param  ?array<string>  $batches
     * @return array<array{notification: string | array<string, mixed>, batch: ?string}>
     */
    protected function consumeEntries(Repository $cache, string $key, ?array $batches): array
    {
        $notifications = [];
        $remaining = [];

        foreach ($cache->get($key) ?? [] as $entry) {
            // Mailboxes published before response ownership was introduced
            // contain bare notifications, which remain unowned catch-up work.
            if (! array_key_exists('notification', $entry)) {
                $entry = ['notification' => $entry, 'batch' => null];
            }

            if (($batches === null) || ($entry['batch'] === null) || in_array($entry['batch'], $batches, strict: true)) {
                $notifications[] = $entry;
            } else {
                $remaining[] = $entry;
            }
        }

        if (count($notifications)) {
            $wasStored = count($remaining)
                ? $cache->put($key, $remaining, now()->addMinutes(max(1, (int) config('session.lifetime', 120))))
                : $cache->forget($key);

            if (! $wasStored) {
                throw new RuntimeException('Could not remove Filament notifications from the mailbox.');
            }
        }

        return $notifications;
    }

    protected function getScope(): string
    {
        $this->initializeScope();

        return session()->get('filament.notifications.mailbox');
    }

    protected function getCacheKey(): string
    {
        return 'filament:notifications:' . $this->getScope();
    }

    protected function mutate(Closure $callback): mixed
    {
        return $this->withLock($callback, shouldWaitForLock: true);
    }

    protected function withLock(Closure $callback, bool $shouldWaitForLock): mixed
    {
        $cache = Cache::store(config('filament.cache_store'));
        $store = $cache->getStore();
        $key = $this->getCacheKey();

        if (! $store instanceof LockProvider) {
            return $callback($cache, $key);
        }

        // Only cache operations run under this lock, never application work,
        // rendering, or waiting for the browser to receive the response.
        $lock = $store->lock("{$key}:lock", 10);

        if ($shouldWaitForLock) {
            return $lock->block(5, fn (): mixed => $callback($cache, $key));
        }

        if (! $lock->get()) {
            throw new LockTimeoutException;
        }

        try {
            return $callback($cache, $key);
        } finally {
            $lock->release();
        }
    }
}
