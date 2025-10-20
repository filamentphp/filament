<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasErrorNotifications
{
    protected bool | Closure $hasErrorNotifications = true;

    /**
     * @var array<array{ title: string | Closure, body: string | Closure | null }>
     */
    protected array $errorNotifications = [];

    public function errorNotifications(bool | Closure $condition = true): static
    {
        $this->hasErrorNotifications = $condition;

        return $this;
    }

    public function hasErrorNotifications(): bool
    {
        return $this->evaluate($this->hasErrorNotifications);
    }

    public function registerErrorNotification(string | Closure $title, string | Closure | null $body = null, ?int $statusCode = null): static
    {
        $this->errorNotifications[$statusCode] = [
            'title' => $title,
            'body' => $body,
        ];

        return $this;
    }

    /**
     * @return array<array{ title: string | Closure, body: string | Closure | null }>
     */
    public function getErrorNotifications(): array
    {
        $notifications = array_map(
            fn (array $notification): array => [
                'title' => $this->evaluate($notification['title']),
                'body' => $this->evaluate($notification['body']),
            ],
            $this->errorNotifications,
        );

        $notifications[null] ??= [
            'title' => __('filament-panels::error-notifications.title'),
            'body' => __('filament-panels::error-notifications.body'),
        ];

        return $notifications;
    }
}
