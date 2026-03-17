<?php

namespace Filament\Panel\Concerns;

use Closure;

trait HasErrorNotifications
{
    protected bool | Closure $hasErrorNotifications = true;

    /**
     * @var array<array{ title: string | Closure | null, body: string | Closure | null, isHidden: bool, isDisabled: bool }>
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
            'isHidden' => false,
            'isDisabled' => false,
        ];

        return $this;
    }

    public function hiddenErrorNotification(int $statusCode): static
    {
        $this->errorNotifications[$statusCode] = [
            'title' => null,
            'body' => null,
            'isHidden' => true,
            'isDisabled' => false,
        ];

        return $this;
    }

    public function disabledErrorNotification(int $statusCode): static
    {
        $this->errorNotifications[$statusCode] = [
            'title' => null,
            'body' => null,
            'isHidden' => false,
            'isDisabled' => true,
        ];

        return $this;
    }

    /**
     * @return array<array{ title: ?string, body: ?string, isHidden: bool, isDisabled: bool }>
     */
    public function getErrorNotifications(): array
    {
        $notifications = array_map(
            fn (array $notification): array => [
                'title' => $this->evaluate($notification['title']),
                'body' => $this->evaluate($notification['body']),
                'isHidden' => $notification['isHidden'],
                'isDisabled' => $notification['isDisabled'],
            ],
            $this->errorNotifications,
        );

        $notifications[''] ??= [
            'title' => __('filament-panels::error-notifications.title'),
            'body' => __('filament-panels::error-notifications.body'),
            'isHidden' => false,
            'isDisabled' => false,
        ];

        return $notifications;
    }
}
