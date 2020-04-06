<?php

namespace Filament\Contracts;

interface Fieldset
{
    /**
     * The fieldset title.
     * 
     * @return string
     */
    public static function title(): string;

    /**
     * The fieldset fields.
     * 
     * @return array
     */
    public static function fields($model): array;

    /**
     * The rules to ignore in real time for the fieldset.
     * 
     * @return array
     */
    public static function rulesIgnoreRealtime(): array;
}