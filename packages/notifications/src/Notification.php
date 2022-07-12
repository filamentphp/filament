<?php

namespace Filament\Notifications;

use Filament\Support\Components\ViewComponent;
use Livewire\Wireable;

class Notification extends ViewComponent implements Wireable
{
    protected string $view = 'notifications::components.notification';

    public string $id;

    public bool $closed = false;

    public string $title;

    public ?string $description = null;

    public ?string $icon = null;

    public ?string $status = null;

    public function __construct()
    {
        $this->id(uniqid());
    }

    public static function make(): self
    {
        $static = app(static::class);
        $static->setUp();
        $static->success();

        return $static;
    }

    public function toLivewire(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'status' => $this->status,
        ];

        // return get_object_vars($this);
    }

    public static function fromLivewire($value): self
    {
        $static = static::make();

        foreach ($value as $key => $value) {
            $static->{$key} = $value;
        }

        return $static;
    }

    protected function setUp(): void
    {
        $this->success();
    }

    public function id(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function closed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function getClosed(): bool
    {
        return $this->closed;
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function success(): self
    {
        $this->status('success');

        return $this;
    }

    public function warning(): self
    {
        $this->status('warning');

        return $this;
    }

    public function danger(): self
    {
        $this->status('danger');

        return $this;
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(): ?string
    {
        return $this->icon ?? match ($this->getStatus()) {
            'success' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-circle',
            'danger' => 'heroicon-o-x-circle',
        };
    }

    public function close(): static
    {
        $this->closed(true);

        return $this;
    }
}
