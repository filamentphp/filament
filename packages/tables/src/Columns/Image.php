<?php

namespace Filament\Tables\Columns;

class Image extends Column
{
    public $width = null;

    public $height = null;

    public string $class = '';

    public bool $rounded = false;

    public bool $cover = false;

    public function width($width): self
    {
        return tap($this, fn () => $this->width = $width);
    }

    public function height($height): self
    {
        return tap($this, fn () => $this->height = $height);
    }

    public function square(int $size): self
    {
        return tap($this, function () use ($size) {
            $this->width = $size;
            $this->height = $size;
        });
    }

    public function class(string $class): self
    {
        return tap($this, fn () => $this->class = $class);
    }

    public function rounded(): self
    {
        return tap($this, fn () => $this->rounded = true);
    }

    public function cover(): self
    {
        return tap($this, fn () => $this->cover = true);
    }

    public function getWidth()
    {
        return $this->width && is_numeric($this->width) ? "{$this->width}px" : $this->width;
    }

    public function getHeight()
    {
        return $this->height && is_numeric($this->height) ? "{$this->height}px" : $this->height;
    }

    public function getClass(): string
    {
        $class = $this->class;

        if ($this->rounded) $class .= ' rounded-full';
        if ($this->cover) $class .= ' bg-cover';

        return $class;
    }
}
