<?php

namespace Filament\Notifications;

use Illuminate\Support\Collection as BaseCollection;
use Livewire\Wireable;

class Collection extends BaseCollection implements Wireable
{
    /**
     * @param  array<array<string, mixed>>  $items
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

    public static function fromLivewire(mixed $value): static
    {
        abort_unless(is_array($value), 419);

        $validateActions = static function (mixed $actions) use (&$validateActions): void {
            abort_unless(is_array($actions), 419);

            foreach ($actions as $action) {
                abort_unless(is_array($action), 419);

                if (array_key_exists('actions', $action)) {
                    $validateActions($action['actions'] ?? []);

                    continue;
                }

                abort_unless(is_string($action['name'] ?? null), 419);
            }
        };

        foreach ($value as $notification) {
            abort_unless(is_array($notification), 419);

            $validateActions($notification['actions'] ?? []);
        }

        return app(static::class, ['items' => $value])->transform(
            fn (array $notification): Notification => Notification::fromArray($notification),
        );
    }
}
