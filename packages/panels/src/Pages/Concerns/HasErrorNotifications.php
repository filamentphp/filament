<?php

namespace Filament\Pages\Concerns;

use Filament\Facades\Filament;
use Filament\Support\Components\Attributes\Unexposed;

trait HasErrorNotifications
{
    protected ?bool $hasErrorNotifications = null;

    /**
     * @var array<array{ title: ?string, body: ?string, isHidden: bool, isDisabled: bool }>
     */
    protected array $errorNotifications = [];

    protected function setUpErrorNotifications(): void {}

    #[Unexposed]
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

    #[Unexposed]
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

    #[Unexposed]
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

    #[Unexposed]
    public function hasErrorNotifications(): bool
    {
        return $this->hasErrorNotifications ??= Filament::hasErrorNotifications();
    }

    /**
     * @return array<array{ title: ?string, body: ?string, isHidden: bool, isDisabled: bool }>
     */
    #[Unexposed]
    public function getErrorNotifications(): array
    {
        $this->errorNotifications = Filament::getErrorNotifications();
        $this->setUpErrorNotifications();

        return $this->errorNotifications;
    }
}
