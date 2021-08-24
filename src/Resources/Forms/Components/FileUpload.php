<?php

namespace Filament\Resources\Forms\Components;

class FileUpload extends \Filament\Forms\Components\FileUpload
{
    use Concerns\InteractsWithResource;

    public $storeAsCallback;

    public function storeAs(callable $storeAsCallback)
    {
        $this->storeAsCallback = $storeAsCallback;

        return $this;
    }
}
