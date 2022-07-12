<?php

namespace Filament\Notifications;

use Illuminate\Support\Collection as BaseCollection;
use Livewire\Wireable;

class Collection extends BaseCollection implements Wireable
{
    public function toLivewire(): array
    {
        return $this
            ->map(fn (Notification $notification): array => $notification->toLivewire())
            ->toArray();
    }

    public static function fromLivewire($value): static
    {
        return (new static($value))->map(
            fn (array $notification): Notification => Notification::fromLivewire($notification),
        );
    }
}
