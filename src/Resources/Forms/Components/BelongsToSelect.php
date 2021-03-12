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
        if (! $this->getOptions || ! $this->model) return;

        $getOptions = $this->getOptions;

        $this->options = $getOptions();

        return $this;
    }

    public function relationship($relationshipName, $displayColumnName, $callback = null)
    {
        $this->getDisplayValueUsing(function ($value) use ($displayColumnName, $relationshipName) {
            $relationship = (new $this->model())->{$relationshipName}();

            $record = $relationship->getRelated()->where($relationship->getOwnerKeyName(), $value)->first();

            return $record ? $record->getAttributeValue($displayColumnName) : null;
        });

        $this->getOptionSearchResultsUsing(function ($search) use ($callback, $displayColumnName, $relationshipName) {
            $relationship = (new $this->model())->{$relationshipName}();

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
            $relationship = (new $this->model())->{$relationshipName}();

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

        $setUpRules = function ($component) use ($relationshipName) {
            $relationship = (new $component->model())->{$relationshipName}();

            $model = get_class($relationship->getModel());
            $column = $relationship->getOwnerKeyName();

            $component->addRules([$component->name => "exists:{$model},{$column}"]);
        };

        if ($this->model) {
            $setUpRules($this);
        }

        return $this;
    }
}
