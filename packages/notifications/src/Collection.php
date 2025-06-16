<?php

namespace Filament\Notifications;

use Illuminate\Support\Collection as BaseCollection;
use Livewire\Wireable;

/**
 * @extends BaseCollection<array-key, Notification>
 */
class Collection extends BaseCollection implements Wireable
{
    /**
     * @param  array<array-key, Notification>  $items
     */
    final public function __construct($items = [])
    {
        parent::__construct($items);
    }

    /**
     * @return array<array<string, mixed>>
     */
    public function toLivewire(): array
    {
        return $this->toArray();
    }

    /**
     * @param  array<array<string, mixed>>  $value
     */
    public static function fromLivewire($value): static
    {
        return app(static::class, ['items' => $value])->transform(
            fn (array $notification): Notification => Notification::fromArray($notification),
        );
    }
}
