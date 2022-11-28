<?php

namespace Filament\Tables\Columns\Contracts;

interface Editable
{
    /**
     * @param mixed $input
     */
    public function validate($input): void;
}
