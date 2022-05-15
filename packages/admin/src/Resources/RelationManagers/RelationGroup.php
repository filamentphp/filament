<?php

namespace Filament\Resources\RelationManagers;

class RelationGroup
{
    public function __construct(
        protected string $label,
        protected array $managers,
    ) {}

    public static function make(string $label, array $managers): static
    {
        return app(static::class, ['label' => $label, 'managers' => $managers]);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getManagers(): array
    {
        return $this->managers;
    }
}
