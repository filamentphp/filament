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

    public static function fromLivewire($value): self
    {
        $self = new self();
        $self->items = $value;
        $self->transform(fn (array $notification): Notification => Notification::fromLivewire($notification));

        return $self;
    }
}
