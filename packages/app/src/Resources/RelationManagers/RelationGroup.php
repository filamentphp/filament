<?php

namespace Filament\Resources\RelationManagers;

use Illuminate\Database\Eloquent\Model;

class RelationGroup
{
    /**
     * @param array<class-string> $managers
     */
    public function __construct(
        protected string $label,
        protected array $managers,
    ) {
    }

    /**
     * @param array<class-string> $managers
     */
    public static function make(string $label, array $managers): static
    {
        return app(static::class, ['label' => $label, 'managers' => $managers]);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return array<class-string>
     */
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
