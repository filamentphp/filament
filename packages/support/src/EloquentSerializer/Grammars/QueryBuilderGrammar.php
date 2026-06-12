<?php

namespace Filament\Support\EloquentSerializer\Grammars;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\JoinClause;

trait QueryBuilderGrammar
{
    /**
     * @return array<string, mixed>
     */
    protected function packQueryBuilder(Builder $builder): array
    {
        return array_filter([
            'bindings' => $builder->bindings,
            'aggregate' => $builder->aggregate,
            'columns' => $builder->columns,
            'distinct' => $builder->distinct,
            'from' => $builder->from,
            'wheres' => $this->packWheres($builder->wheres),
            'groups' => $builder->groups,
            'havings' => $this->packWheres($builder->havings),
            'groupLimit' => $builder->groupLimit ?? null,
            'orders' => $builder->orders,
            'limit' => $builder->limit,
            'offset' => $builder->offset,
            'unions' => $this->packUnions($builder->unions),
            'unionLimit' => $builder->unionLimit,
            'unionOffset' => $builder->unionOffset,
            'unionOrders' => $builder->unionOrders,
            'lock' => $builder->lock,

            'joins' => $this->packJoins($builder->joins), // must be the last
        ], fn ($item) => isset($item));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function unpackQueryBuilder(array $data, Builder $builder): Builder
    {
        foreach ($data as $key => $value) {
            if (in_array($key, ['wheres', 'havings'])) {
                $value = $this->unpackWheres($value, $builder);
            }

            if ($key == 'unions') {
                $value = $this->unpackUnions($value);
            }

            if ($key == 'joins') {
                $value = $this->unpackJoins($value, $builder);
            }

            if (is_array($builder->$key) && is_array($value)) {
                $builder->$key = array_merge_recursive($builder->$key, $value);
            } else {
                $builder->$key = $value;
            }
        }

        return $builder;
    }

    private function packWheres(mixed $wheres): mixed
    {
        if (is_null($wheres)) {
            return $wheres;
        }

        foreach ($wheres as &$item) {
            if (isset($item['query'])) {
                $item['query'] = $this->packQueryBuilder($item['query']);
            }
        }
        unset($item);

        return $wheres;
    }

    private function packUnions(mixed $unions): mixed
    {
        if (! is_array($unions)) {
            return $unions;
        }

        foreach ($unions as &$item) {
            if (isset($item['query'])) {
                $item['query'] = $this->pack($item['query']);
            }
        }
        unset($item);

        return $unions;
    }

    private function packJoins(mixed $joins): mixed
    {
        if (! is_array($joins)) {
            return $joins;
        }

        foreach ($joins as &$item) {
            $item = array_replace(
                ['type' => $item->type, 'table' => $item->table],
                $this->packQueryBuilder($item)
            );
        }
        unset($item);

        return $joins;
    }

    private function unpackWheres(mixed $wheres, Builder $builder): mixed
    {
        if (is_null($wheres)) {
            return $wheres;
        }

        foreach ($wheres as &$item) {
            if (isset($item['query'])) {
                $item['query'] = $this->unpackQueryBuilder($item['query'], $builder->newQuery());
            }
        }
        unset($item);

        return $wheres;
    }

    private function unpackUnions(mixed $unions): mixed
    {
        if (! is_array($unions)) {
            return $unions;
        }

        foreach ($unions as &$item) {
            if (isset($item['query'])) {
                $item['query'] = $this->unpack($item['query']);
            }
        }
        unset($item);

        return $unions;
    }

    private function unpackJoins(mixed $joins, Builder $builder): mixed
    {
        if (! is_array($joins)) {
            return $joins;
        }

        foreach ($joins as &$item) {
            $parentQuery = new JoinClause($builder, $item['type'], $item['table']);
            unset($item['type'], $item['table']);

            $item = $this->unpackQueryBuilder($item, $parentQuery);
        }
        unset($item);

        return $joins;
    }
}
