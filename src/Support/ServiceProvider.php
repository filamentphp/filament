<?php

namespace Alpine\Support;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Arr;

abstract class ServiceProvider extends BaseServiceProvider
{
    /**
     * Directory path to this package
     *
     * @var string
     */
    protected $packagePath = __DIR__.'/../../';

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param  string  $path
     * @param  string  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        $config = $this->app['config']->get($key, []);

        $this->app['config']->set($key, $this->mergeConfig(require $path, $config));
    }

    /**
     * Merge the existing configuration with the given configuration.
     *
     * @param  string  $key
     * @param  string  $path
     * @return void
     */
    protected function mergeFromConfig($key, $config)
    {
        $this->app['config']->set($key, $this->mergeConfig($this->app['config']->get($key, []), $config));
    }

    /**
     * Merges the configs together and takes multi-dimensional arrays into account.
     *
     * @param  array  $original
     * @param  array  $merging
     * @return array
     */
    protected function mergeConfig(array $original, array $merging)
    {
        $array = array_merge($original, $merging);

        foreach ($original as $key => $value) {
            if (! is_array($value)) {
                continue;
            }

            if (! Arr::exists($merging, $key)) {
                continue;
            }

            if (is_numeric($key)) {
                continue;
            }

            $array[$key] = $this->mergeConfig($value, $merging[$key]);
        }

        return $array;
    }
}
    