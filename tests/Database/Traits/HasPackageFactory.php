<?php

namespace Filament\Tests\Database\Traits;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

trait HasPackageFactory
{
    use HasFactory;

    protected static function newFactory()
    {
        $modelName = Str::after(get_called_class(), 'Models\\');
        $path = "FilamentManager\\Tests\\Database\\Factories\\{$modelName}Factory";

        return $path::new();
    }
}
