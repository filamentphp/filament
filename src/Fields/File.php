<?php

namespace Filament\Fields;

use Filament\Fields\Concerns;

class File extends InputField
{
    use Concerns\CanBeDisabled;

    public $directory;

    public $disk;

    public $visibility = 'public';

    public function __construct($name)
    {
        parent::__construct($name);

        $this->disk(config('filesystems.default'));
    }

    public function directory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    public function visibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }
}
