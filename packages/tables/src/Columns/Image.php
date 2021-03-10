<?php

namespace Filament\Tables\Columns;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Image extends Column
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    public $disk;

    public $height = 40;

    public $width;

    public $rounded = false;

    protected function setUp()
    {
        $this->disk(config('forms.default_filesystem_disk'));
    }

    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    public function height($height)
    {
        $this->height = $height;

        return $this;
    }

    public function getHeight()
    {
        if ($this->height === null) return null;

        if (is_integer($this->height)) return "{$this->height}px";

        return $this->height;
    }

    public function getWidth()
    {
        if ($this->width === null) return null;

        if (is_integer($this->width)) return "{$this->width}px";

        return $this->width;
    }

    public function getPath($record)
    {
        $path = $this->getValue($record);

        if (! $path) return null;

        if (filter_var($path, FILTER_VALIDATE_URL) !== false) {
            return $path;
        }

        if ($this->disk == 's3' && Storage::disk($this->disk)->getVisibility($path) == 'private') {
            return Storage::disk('s3')->temporaryUrl(
                $path,
                Carbon::now()->addMinutes(5)
            );
        }

        return Storage::disk($this->disk)->url($path);
    }

    public function rounded()
    {
        $this->rounded = true;

        return $this;
    }

    public function size($size)
    {
        $this->width = $size;
        $this->height = $size;

        return $this;
    }

    public function width($width)
    {
        $this->width = $width;

        return $this;
    }
}
