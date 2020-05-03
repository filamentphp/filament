<?php

namespace Filament\Traits;

trait ManipulatesModels 
{
    /**
     * Merge fillable attributes.
     * 
     * @param array $fillable
     * @return void
     */
    protected function mergeFillable(array $fillable)
    {
        $this->fillable = array_merge($fillable, $this->fillable);
    }

    /**
     * Cast an attribute to a native PHP type.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return mixed
     */
    protected function castAttribute($key, $value)
    {
        if ($this->getCastType($key) === 'array' && $value === null) {
            return [];
        }

        return parent::castAttribute($key, $value);
    }
}