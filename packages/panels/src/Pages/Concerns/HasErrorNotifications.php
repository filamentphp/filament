<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;

trait HasErrorNotifications
{
    protected ?bool $hasErrorNotifications = null;

    /**
     * @var array<array{ title: ?string, body: ?string, isHidden: bool, isDisabled: bool }>
     */
    protected array $errorNotifications = [];

    protected function setUpErrorNotifications(): void {}

    public function registerErrorNotification(string $title, ?string $body = null, ?int $statusCode = null): static
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

    public function hasErrorNotifications(): bool
    {
        return $this->hasErrorNotifications ??= Filament::hasErrorNotifications();
    }

    /**
     * @return array<array{ title: ?string, body: ?string, isHidden: bool, isDisabled: bool }>
     */
    public function getErrorNotifications(): array
    {
        $this->errorNotifications = Filament::getErrorNotifications();
        $this->setUpErrorNotifications();

        return $this->errorNotifications;
    }
}
