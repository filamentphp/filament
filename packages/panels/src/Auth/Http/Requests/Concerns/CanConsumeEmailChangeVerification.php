<?php

namespace Filament\Auth\Http\Requests\Concerns;

trait CanConsumeEmailChangeVerification
{
    protected function consumeEmailChangeVerification(string $signature): bool
    {
        $cache = cache();
        $cacheKey = "filament:auth:email_change:verification:{$signature}";

        return (bool) $cache->lock("{$cacheKey}:lock", 60)->get(function () use ($cache, $cacheKey, $signature): bool {
            $wasPending = false;
            $wasRemoved = true;

            foreach ([$cacheKey, $signature] as $key) {
                if (! $cache->has($key)) {
                    continue;
                }

                $wasPending = true;

                if ((! $cache->forget($key)) && ($cache->get($key) !== null)) {
                    $wasRemoved = false;
                }
            }

            return $wasPending && $wasRemoved;
        });
    }
}
