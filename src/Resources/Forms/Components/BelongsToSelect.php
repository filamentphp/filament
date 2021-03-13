<?php

namespace Filament\Resources\Forms\Components;

use Illuminate\Support\Str;

class BelongsToSelect extends Select
{
    protected $getOptions;

    protected $displayColumnName;

    protected $relationship;

    protected $view = 'forms::components.select';

    public function getDisplayColumnName()
    {
        return $this->displayColumnName;
    }

    public function getLabel()
    {
        if ($this->label === null) {
            return (string) Str::of($this->getRelationshipName())
                ->before('.')
                ->kebab()
                ->replace(['-', '_'], ' ')
                ->ucfirst();
        }

        return parent::getLabel();
    }

    public function getOptions()
    {
        if ($callback = $this->getOptions) {
            return $callback();
        }

        return parent::getOptions();
    }

    public function getOptionsUsing($callback)
    {
        $this->configure(function () use ($callback) {
            $this->getOptions = $callback;
        });

        return $this;
    }

    public function getRelationship()
    {
        $model = $this->getModel();

        return (new $model())->{$this->getRelationshipName()}();
    }

    public function getRelationshipName()
    {
        return $this->relationship;
    }

    public function preload()
    {
        $this->configure(function () {
            $options = $this->getOptions();

            if (! $options || ! $this->getModel()) return;

            $this->options = $options;
        });

        return $this;
    }

    public function relationship($relationshipName, $displayColumnName, $callback = null)
    {
        $this->configure(function () use ($callback, $displayColumnName, $relationshipName) {
            $this->displayColumnName = $displayColumnName;
            $this->relationship = $relationshipName;

            $this->getDisplayValueUsing(function ($value) {
                $relationship = $this->getRelationship();

                $record = $relationship->getRelated()->where($relationship->getOwnerKeyName(), $value)->first();

                return $record ? $record->getAttributeValue($this->getDisplayColumnName()) : null;
            });

            $this->getOptionSearchResultsUsing(function ($search) use ($callback) {
                $relationship = $this->getRelationship();

                $query = $relationship->getRelated();

                if ($callback) {
                    $query = $callback($query);
                }

                $search = Str::lower($search);
                $searchOperator = [
                    'pgsql' => 'ilike',
                ][$query->getConnection()->getDriverName()] ?? 'like';

                return $query
                    ->where($this->getDisplayColumnName(), $searchOperator, "%{$search}%")
                    ->pluck($this->getDisplayColumnName(), $relationship->getOwnerKeyName())
                    ->toArray();
            });

            $this->getOptionsUsing(function () use ($callback) {
                $relationship = $this->getRelationship();

                $query = $relationship->getRelated();

                if ($callback) {
                    $query = $callback($query);
                }

                return $query
                    ->pluck($this->getDisplayColumnName(), $relationship->getOwnerKeyName())
                    ->toArray();
            });

            if ($model = $this->getModel()) {
                $relationship = $this->getRelationship();

                $relatedModelClass = get_class($relationship->getModel());

                $this->addRules([$this->getName() => "exists:{$relatedModelClass},{$relationship->getOwnerKeyName()}"]);
            }
        });

        return $this;
    }
}
