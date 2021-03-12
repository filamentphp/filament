<?php

namespace Filament\Tables;

use Illuminate\Support\Str;
use Illuminate\Support\Traits\Tappable;

class Filter
{
    use Tappable;

    protected $callback;

    protected $configurationQueue = [];

    protected $hidden = false;

    protected $label;

    protected $name;

    protected $table;

    public function __construct($name = null, $callback = null)
    {
        if ($name) {
            $this->name($name);
        }

        $this->callback($callback);

        $this->setUp();
    }

    protected function setUp()
    {
        //
    }

    public static function make($name = null, $callback = null)
    {
        return new static($name, $callback);
    }

    public function apply($query)
    {
        $callback = $this->callback;

        return $callback($query);
    }

    public function callback($callback)
    {
        $this->configure(function () use ($callback) {
            $this->callback = $callback;
        });

        return $this;
    }

    public function configure($callback = null)
    {
        if ($callback === null) {
            foreach ($this->configurationQueue as $callback) {
                $callback();
            }

            $this->configurationQueue = [];

            return;
        }

        if ($this->getTable()) {
            $callback();
        } else {
            $this->configurationQueue[] = $callback;
        }

        return $this;
    }

    public function except($contexts, $callback = null)
    {
        $this->configure(function () use ($callback, $contexts) {
            if (! is_array($contexts)) $contexts = [$contexts];

            if (! $callback) {
                $this->hidden();

                $callback = fn ($filter) => $filter->visible();
            }

            if (! $this->getContext() || in_array($this->getContext(), $contexts)) return $this;

            $callback($this);
        });

        return $this;
    }

    public function hidden()
    {
        $this->configure(function () {
            $this->hidden = true;
        });

        return $this;
    }

    public function getLabel()
    {
        if ($this->label === null) {
            return (string) Str::of($this->getName())
                ->kebab()
                ->replace(['-', '_', '.'], ' ')
                ->ucfirst();
        }

        return $this->label;
    }

    public function label($label)
    {
        $this->configure(function () use ($label) {
            $this->label = $label;
        });

        return $this;
    }

    public function name($name)
    {
        $this->configure(function () use ($name) {
            $this->name = $name;
        });
    }

    public function only($contexts, $callback = null)
    {
        $this->configure(function () use ($callback, $contexts) {
            if (! is_array($contexts)) $contexts = [$contexts];

            if (! $callback) {
                $this->hidden();

                $callback = fn ($filter) => $filter->visible();
            }

            if (! in_array($this->getContext(), $contexts)) return $this;

            $callback($this);
        });

        return $this;
    }

    public function table($table)
    {
        $this->table = $table;

        $this->configure();

        return $this;
    }
}
