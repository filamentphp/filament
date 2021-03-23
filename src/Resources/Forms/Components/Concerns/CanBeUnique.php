<?php

namespace Filament\Resources\Forms\Components\Concerns;

use Illuminate\Database\Eloquent\Model;

trait CanBeUnique
{
    use \Filament\Forms\Components\Concerns\CanBeUnique;

    public function unique($table, $column = null, $except = false)
    {
        $this->configure(function () use ($column, $except, $table) {
            $rule = "unique:$table,$column";

            $record = $this->getLivewire()->record;

            if ($except && $record instanceof Model) {
                $rule .= ",{$record->getKey()}";
            }

            $this->addRules([$this->getName() => [$rule]]);
        });

        return $this;
    }
}
