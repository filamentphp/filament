<?php

namespace Filament\Traits;

trait CastsAttributes 
{
    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        if ($this->getCastType($key) === 'array' && is_null($value)) {
            return [];
        }

        return parent::castAttribute($key, $value);
    }
}