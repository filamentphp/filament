<?php

namespace Filament\Models;

use Filament\Sushi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;
use Symfony\Component\Finder\SplFileInfo;

class Resource extends Model
{
    use Sushi;

    protected $schema = [
        'className' => 'string',
        'icon' => 'string',
        'label' => 'string',
        'model' => 'string',
        'slug' => 'string',
        'sort' => 'integer',
    ];

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
        $filesystem = new Filesystem();

        if (! $filesystem->isDirectory(config('filament.resources.path'))) return [];

        return collect($filesystem->allFiles(config('filament.resources.path')))
            ->map(function (SplFileInfo $file) {
                return (string) Str::of(config('filament.resources.namespace'))
                    ->append('\\', $file->getRelativePathname())
                    ->replace(['/', '.php'], ['\\', '']);
            })
            ->filter(function ($class) {
                return is_subclass_of($class, \Filament\Resource::class) && ! (new ReflectionClass($class))->isAbstract();
            })
            ->map(function ($class) {
                return [
                    'className' => $class,
                    'icon' => $class::getIcon(),
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
