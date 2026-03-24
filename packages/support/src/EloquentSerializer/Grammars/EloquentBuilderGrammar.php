<?php

namespace Filament\Support\EloquentSerializer\Grammars;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Laravel\SerializableClosure\SerializableClosure;

trait EloquentBuilderGrammar
{
    /**
     * @param  array<class-string>|null  $parentModels
     * @return array<string, mixed>
     */
    protected function packEloquentBuilder(EloquentBuilder $builder, ?array $parentModels = null): array
    {
        return [
            'with' => $this->getEagers($builder, $parentModels ?? []), // preloaded ("eager") relations
            'removed_scopes' => $builder->removedScopes(), // global scopes
            'casts' => $builder->getModel()->getCasts(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function unpackEloquentBuilder(array $data, EloquentBuilder &$builder): void
    {
        // Preloaded ("eager") relations
        $this->setEagers($builder, $data['with']);

        // Global scopes
        if ($data['removed_scopes']) {
            $builder->withoutGlobalScopes($data['removed_scopes']);
        }

        // Casts
        $builder->getModel()->mergeCasts($data['casts']);
    }

    /**
     * @param  array<class-string>  $parentModels
     * @return array<string, mixed>
     */
    private function getEagers(EloquentBuilder $builder, array $parentModels): array
    {
        $result = [];

        foreach ($builder->getEagerLoads() as $name => $value) {
            $relation = $builder;
            foreach (explode('.', $name) as $part) {
                $relation = $relation->getRelation($part); // get a relation without "constraints"
            }
            $referenceRelation = clone $relation;

            if (count(array_filter($parentModels, fn ($item) => $item == get_class($referenceRelation->getModel()))) > 1) {
                $result[$name] = ['closure' => serialize(new SerializableClosure($value))]; // recursion detected...

                continue;
            }
            $parentModels[] = get_class($builder->getModel());

            $value($relation); // apply closure
            $result[$name] = [
                'query' => $this->packQueryBuilder($relation->getQuery()->getQuery()),
                'eloquent' => $this->packEloquentBuilder($relation->getQuery(), $parentModels),
                'extra' => $this->getExtraRelationParameters($relation),
            ];

            $relation->getQuery()->getModel()->newInstance()->with($name)->getEagerLoads()[$name]($referenceRelation);
            $this->cleanStaticConstraints($result[$name]['query'], $this->packQueryBuilder($referenceRelation->getQuery()->getQuery()));
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $eagers
     */
    private function setEagers(EloquentBuilder $builder, array $eagers): void
    {
        foreach ($eagers as &$value) {
            if (isset($value['closure'])) {
                $value = unserialize($value['closure'])->getClosure();

                continue;
            }

            $value = function ($query) use ($value): void {
                if (isset($value['extra']) && $query instanceof Relation) {
                    $this->setExtraRelationParameters($query, $value['extra']);
                }

                // Input argument may be different depends on context
                while (! ($query instanceof EloquentBuilder)) {
                    $query = $query->getQuery();
                }
                if (isset($value['eloquent'])) {
                    $this->unpackEloquentBuilder($value['eloquent'], $query);
                }

                // Input argument may be different depends on context
                while (! ($query instanceof QueryBuilder)) {
                    $query = $query->getQuery();
                }

                $this->unpackQueryBuilder(isset($value['query']) ? $value['query'] : $value, $query);
            };
        }
        unset($value);

        $builder->setEagerLoads($eagers);
    }

    /**
     * @param  array<string, mixed>  $packedQueryBuilder
     * @param  array<string, mixed>  $packedReferenceQueryBuilder
     */
    private function cleanStaticConstraints(array &$packedQueryBuilder, array $packedReferenceQueryBuilder): void
    {
        $properties = [
            'aggregate', 'columns', 'distinct', 'wheres', 'groups', 'havings', 'orders', 'limit', 'offset', 'unions',
            'unionLimit', 'unionOffset', 'unionOrders', 'joins', 'groupLimit',
        ];

        foreach ($properties as $property) {
            if (! is_array($packedQueryBuilder[$property] ?? null)) {
                continue;
            }

            foreach ($packedQueryBuilder[$property] as $key => $item) {
                if (in_array($item, (array) ($packedReferenceQueryBuilder[$property] ?? null), true)) {
                    unset($packedQueryBuilder[$property][$key]);
                }
            }
        }

        foreach ($packedQueryBuilder['bindings'] as $binding => $data) {
            if (! is_array($data)) {
                continue;
            }

            foreach ($data as $key => $value) {
                if (
                    isset($packedReferenceQueryBuilder['bindings'][$binding][$key])
                    && $packedReferenceQueryBuilder['bindings'][$binding][$key] === $value
                ) {
                    unset($packedQueryBuilder['bindings'][$binding][$key]);
                }
            }
        }
    }
}
