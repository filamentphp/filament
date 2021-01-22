<?php

namespace Filament\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use Sushi\Sushi;
use Symfony\Component\Finder\SplFileInfo;

class Resource extends Model
{
    use Sushi;

    public function authorizationManager()
    {
        return $this->class()::authorizationManager();
    }

    public function class()
    {
        return $this->attributes['className'];
    }

    public function getRows()
    {
        return collect((new Filesystem())->allFiles(app_path('Filament/Resources')))
            ->map(function (SplFileInfo $file) {
                return (string) Str::of('App\\Filament\\Resources')
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, \Filament\Resource::class) && ! (new ReflectionClass($class))->isAbstract();
            })
            ->map(function ($class) {
                return [
                    'className' => $class,
                    'icon' => $class::$icon,
                    'label' => $class::getLabel(),
                    'model' => $class::$model,
                    'slug' => $class::getSlug(),
                    'sort' => $class::$sort,
                ];
            })
            ->toArray();
    }

    public function router()
    {
        return $this->class()::router();
    }
}
