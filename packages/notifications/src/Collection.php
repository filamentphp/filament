<?php

namespace Filament\Notifications;

use Illuminate\Support\Collection as BaseCollection;
use Livewire\Wireable;

class Collection extends BaseCollection implements Wireable
{
    private const MAX_ACTION_GROUP_DEPTH = 10;

    private const MAX_RESTORATION_NODES = 100;

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

        // Share the restoration budget across calls and components in one request.
        $remainingNodes = request()->attributes->getInt('filament.notifications.remainingNodes', self::MAX_RESTORATION_NODES);

        abort_if(count($value) > $remainingNodes, 419);

        $remainingNodes -= count($value);

        $validateActions = static function (mixed $actions, int $depth = 0) use (&$validateActions, &$remainingNodes): void {
            abort_unless(is_array($actions), 419);
            abort_if(($depth > self::MAX_ACTION_GROUP_DEPTH) || (count($actions) > $remainingNodes), 419);

            $remainingNodes -= count($actions);

            foreach ($actions as $action) {
                abort_unless(is_array($action), 419);

                if (array_key_exists('actions', $action)) {
                    $validateActions($action['actions'] ?? [], $depth + 1);

                    continue;
                }

                abort_unless(is_string($action['name'] ?? null), 419);
            }
        };

        foreach ($value as $notification) {
            abort_unless(is_array($notification), 419);

            $validateActions($notification['actions'] ?? []);
        }

        request()->attributes->set('filament.notifications.remainingNodes', $remainingNodes);

        return app(static::class, ['items' => $value])->transform(
            fn (array $notification): Notification => Notification::fromArray($notification),
        );
    }
}
