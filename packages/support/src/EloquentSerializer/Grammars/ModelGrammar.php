<?php

namespace Filament\Support\EloquentSerializer\Grammars;

use Filament\Support\EloquentSerializer\Package;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasOneOrMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\Relation;
use RuntimeException;

use function Livewire\invade;

trait ModelGrammar
{
    protected function pack(Builder $builder): Package
    {
        // Global scopes handle
        $builder = $builder->applyScopes();

        /** @var array<string, object> $scopes */
        $scopes = invade($builder)->scopes;
        $builder->withoutGlobalScopes(array_keys($scopes));

        return new Package([
            'model' => get_class($builder->getModel()),
            'connection' => $builder->getModel()->getConnectionName(),
            'eloquent' => $this->packEloquentBuilder($builder),
            'query' => $this->packQueryBuilder($builder->getQuery()),
        ]);
    }

    protected function unpack(Package $package): Builder
    {
        $builder = $package->get('model');
        $builder = $builder::on($package->get('connection'));

        $this->unpackEloquentBuilder($package->get('eloquent'), $builder);
        $this->unpackQueryBuilder($package->get('query'), $builder->getQuery());

        return $builder;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getExtraRelationParameters(Relation $relation): ?array
    {
        if ($relation instanceof MorphTo) {
            $invaded = invade($relation);

            return [
                'morphableEagerLoads' => $this->serializeMorphableEager($invaded->morphableEagerLoads),
                'morphableEagerLoadCounts' => $this->serializeMorphableEager($invaded->morphableEagerLoadCounts),
                'morphableConstraints' => $invaded->morphableConstraints,
            ];
        }

        if ($relation instanceof HasOneOrMany) {
            return [
                'inverseRelationship' => invade($relation)->inverseRelationship,
            ];
        }

        return null;
    }

    /**
     * @param  array<string, mixed>|null  $params
     */
    private function setExtraRelationParameters(Relation $relation, ?array $params): void
    {
        if ($params === null) {
            return;
        }

        $invaded = invade($relation);

        foreach ($params as $key => $value) {
            if ($key === 'morphableEagerLoads' || $key === 'morphableEagerLoadCounts') {
                $value = $this->unserializeMorphableEager($value);
            }

            $invaded->{$key} = $value;
        }
    }

    /**
     * @param  array<class-string, array<string, mixed>>  $value
     * @return array<class-string, array<string, mixed>>
     */
    private function serializeMorphableEager(array $value): array
    {
        foreach ($value as $class => &$items) {
            foreach ($items as $relation => &$item) {
                if (! is_callable($item)) {
                    continue;
                }

                if (! method_exists($class, $relation)) {
                    throw new RuntimeException("Serialization error. Does relation '{$relation}' exists in the model '{$class}' ?");
                }

                $eloquentBuilder = (new $class)->{$relation}()->getModel()->newQuery();
                $item($eloquentBuilder);

                $item = [
                    'eloquent' => $this->packEloquentBuilder($eloquentBuilder),
                    'builder' => $this->packQueryBuilder($eloquentBuilder->getQuery()),
                ];
            }
            unset($item);
        }
        unset($items);

        return $value;
    }

    /**
     * @param  array<class-string, array<string, mixed>>  $value
     * @return array<class-string, array<string, mixed>>
     */
    private function unserializeMorphableEager(array $value): array
    {
        foreach ($value as &$items) {
            foreach ($items as &$item) {
                if (! is_array($item)) {
                    continue;
                }

                $item = function ($query) use ($item): void {
                    if ($query instanceof Builder) {
                        $this->unpackEloquentBuilder($item['eloquent'], $query);
                        $this->unpackQueryBuilder($item['builder'], $query->getQuery());
                    } else {
                        $eloquent = $query->getQuery();
                        $this->unpackEloquentBuilder($item['eloquent'], $eloquent);
                        $this->unpackQueryBuilder($item['builder'], $query->getBaseQuery());
                    }
                };
            }
            unset($item);
        }
        unset($items);

        return $value;
    }
}
