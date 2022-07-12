<?php

namespace Filament\Notifications;

use Filament\Support\Components\ViewComponent;
use Livewire\Wireable;
use Illuminate\Support\Str;

class Notification extends ViewComponent implements Wireable
{
    protected string $view = 'notifications::components.notification';

    public string $id;

    public string $title;

    public ?string $description = null;

    public ?string $icon = null;

    public ?string $status = null;

    public function __construct()
    {
        $this->id(Str::uuid());
    }

    public static function make(): static
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

    public static function fromLivewire($value): static
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

    public function id(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function description(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function status(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function success(): static
    {
        $this->status('success');

        return $this;
    }

    public function warning(): static
    {
        $this->status('warning');

        return $this;
    }

    public function danger(): static
    {
        $this->status('danger');

        return $this;
    }

    public function icon(string $icon): static
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
}
