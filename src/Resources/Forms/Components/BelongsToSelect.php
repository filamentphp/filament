<?php

namespace Filament\Resources\Forms\Components;

use Illuminate\Support\Str;

class BelongsToSelect extends Select
{
    public $getOptions;

    public $view = 'forms::components.select';

    public function getOptionsUsing($callback)
    {
        $this->getOptions = $callback;

        return $this;
    }

    public function preload()
    {
        $options = $this->getOptions();

        if (! $options || ! $this->getModel()) return;

        $this->options = $options;

        return $this;
    }

    public function relationship($relationshipName, $displayColumnName, $callback = null)
    {
        $this->getDisplayValueUsing(function ($value) use ($displayColumnName, $relationshipName) {
            $model = $this->getModel();

            $relationship = (new $model())->{$relationshipName}();

            $record = $relationship->getRelated()->where($relationship->getOwnerKeyName(), $value)->first();

            return $record ? $record->getAttributeValue($displayColumnName) : null;
        });

        $this->getOptionSearchResultsUsing(function ($search) use ($callback, $displayColumnName, $relationshipName) {
            $model = $this->getModel();

            $relationship = (new $model())->{$relationshipName}();

            $query = $relationship->getRelated();

            if ($callback) {
                $query = $callback($query);
            }

            $search = Str::lower($search);
            $searchOperator = [
                'pgsql' => 'ilike',
            ][$query->getConnection()->getDriverName()] ?? 'like';

            return $query
                ->where($displayColumnName, $searchOperator, "%{$search}%")
                ->pluck($displayColumnName, $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->getOptionsUsing(function () use ($callback, $displayColumnName, $relationshipName) {
            $model = $this->getModel();

            $relationship = (new $model())->{$relationshipName}();

            $query = $relationship->getRelated();

            if ($callback) {
                $query = $callback($query);
            }

            return $query
                ->pluck($displayColumnName, $relationship->getOwnerKeyName())
                ->toArray();
        });

        $this->label(
            (string) Str::of($relationshipName)
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst(),
        );

        return $this;
    }
}
