<?php

namespace Filament\Support\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;

class RecordChangedBroadcast implements ShouldBroadcast
{
    protected string $channel;

    public function __construct(Model $record)
    {
        $this->channel = static::getChannel($record);
    }

    public static function getChannel(Model $record): string
    {
        return 'filament.records.' . hash('sha256', serialize([
            $record::class,
            $record->getConnection()->getName(),
            $record->getConnection()->getDatabaseName(),
            (string) $record->getKey(),
        ]));
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel($this->channel);
    }

    public function broadcastAs(): string
    {
        return 'filament.record-changed';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [];
    }
}
