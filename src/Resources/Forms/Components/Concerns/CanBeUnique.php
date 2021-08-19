<?php

namespace Filament\Resources\Forms\Components\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

trait CanBeUnique
{
    use \Filament\Forms\Components\Concerns\CanBeUnique;

    public function unique($table, $column = null, $except = false)
    {
        $this->configure(function () use ($column, $except, $table) {
            $rule = Rule::unique($table, $column);

            $record = $this->getLivewire()->record;

            if ($except && $record instanceof Model) {
                $rule->ignore($record->getOriginal($record->getKeyName()), $record->getKeyName());
            }

            $this->addRules([$this->getName() => [$rule]]);
        });

        return $this;
    }
}
