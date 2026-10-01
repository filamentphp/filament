<?php

namespace Filament\Support\Livewire\Concerns;

use Illuminate\Database\Eloquent\Model;

trait ResolvesScopedModelProperties
{
    protected bool $hasResolvedScopedModelPropertiesForRequest = false;

    public function mountResolvesScopedModelProperties(): void
    {
        $this->hasResolvedScopedModelPropertiesForRequest = false;

        $this->resolveScopedModelProperties();
    }

    /** @param array<string, mixed> | null $properties */
    public function resolveScopedModelProperties(?array $properties = null): void
    {
        if (($properties === null) && $this->hasResolvedScopedModelPropertiesForRequest) {
            return;
        }

        $hasModel = false;

        foreach ($this->getScopedModelPropertyNames() as $property) {
            $model = $properties === null
                ? ($this->{$property} ?? null)
                : ($properties[$property] ?? null);

            if ((! ($model instanceof Model)) || (! $model->exists)) {
                continue;
            }

            $currentModel = $this->{$property} ?? null;

            if (($properties !== null) && ($currentModel instanceof Model)) {
                abort_unless(
                    ($currentModel::class === $model::class) && ((string) $currentModel->getKey() === (string) $model->getKey()),
                    404,
                );
            }

            $hasModel = true;
            $resolvedModel = $model->newQuery()
                ->useWritePdo()
                ->find($model->getKey());

            abort_unless($resolvedModel !== null, 404);

            $this->{$property} = $resolvedModel;
        }

        $this->hasResolvedScopedModelPropertiesForRequest = $hasModel;
    }

    /** @return array<string> */
    abstract protected function getScopedModelPropertyNames(): array;
}
