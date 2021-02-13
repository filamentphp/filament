<?php

namespace Filament\Fields;

use Filament\Fields\Concerns;
use Illuminate\Support\Str;

class File extends InputField
{
    use Concerns\CanBeDisabled;
    use Concerns\HasPlaceholder;

    public $acceptedFileTypes = [];

    public $directory;

    public $disk;

    public $maxSize;

    public $minSize;

    public $visibility = 'public';

    public function __construct($name)
    {
        parent::__construct($name);

        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ['nullable', 'file']]);
        $this->disk(config('filament.default_filesystem_disk'));
    }

    public function acceptedFileTypes($types)
    {
        if (! is_array($types)) $types = explode(',', $types);

        $this->acceptedFileTypes = $types;

        $types = implode(',', $types);

        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["mimetypes:{$types}"]]);

        return $this;
    }

    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    public function directory($directory)
    {
        $this->directory = $directory;

        return $this;
    }

    public function getTemporaryUploadedFilePropertyName()
    {
        return "temporaryUploadedFiles.{$this->name}";
    }

    public function getValidationAttributes()
    {
        $attributes = parent::getValidationAttributes();

        $attributes = array_merge(
            $attributes,
            [$this->getTemporaryUploadedFilePropertyName() => Str::lower($this->label)],
        );

        return $attributes;
    }

    public function image()
    {
        $this->acceptedFileTypes([
            'image/*',
        ]);

        return $this;
    }

    public function maxSize($size)
    {
        $this->maxSize = $size;

        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["max:{$this->maxSize}"]]);

        return $this;
    }

    public function minSize($size)
    {
        $this->minSize = $size;

        $this->addRules([$this->getTemporaryUploadedFilePropertyName() => ["min:{$this->minSize}"]]);

        return $this;
    }

    public function visibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }
}
