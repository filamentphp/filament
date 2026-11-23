<?php

namespace Filament\Auth\Http\Requests\Concerns;

trait CanConsumeEmailChangeVerification
{
    protected function consumeEmailChangeVerification(string $signature): bool
    {
        $cache = cache();
        $cacheKey = "filament:auth:email_change:verification:{$signature}";

        return (bool) $cache->lock("{$cacheKey}:lock", 60)->get(function () use ($cache, $cacheKey): bool {
            if (! $cache->has($cacheKey)) {
                return false;
            }

            return $cache->forget($cacheKey) || ($cache->get($cacheKey) === null);
        });
    }
}
