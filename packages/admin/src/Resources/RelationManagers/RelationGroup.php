<?php

namespace Filament\Resources\RelationManagers;

use Illuminate\Database\Eloquent\Model;

class RelationGroup
{
    public function __construct(
        protected string $label,
        protected array $managers,
    ) {
    }

    public static function make(string $label, array $managers): static
    {
        return app(static::class, ['label' => $label, 'managers' => $managers]);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getManagers(?Model $ownerRecord = null): array
    {
        if (! $ownerRecord) {
            return $this->managers;
        }

        return array_filter(
            $this->managers,
            fn (string $manager): bool => $manager::canViewForRecord($ownerRecord),
        );
    }
}
