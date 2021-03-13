<?php

namespace Filament\Forms\Components\Concerns;

trait CanBeUnique
{
    public function unique($table, $column = null, $exceptCurrentRecord = false)
    {
        $this->configure(function () use ($column, $exceptCurrentRecord, $table) {
            $rule = "unique:$table,$column";
            if ($exceptCurrentRecord) {
                $rule .= ',{{record}}';
            }

            $this->addRules([$this->getName() => [$rule]]);
        });

        return $this;
    }
}
