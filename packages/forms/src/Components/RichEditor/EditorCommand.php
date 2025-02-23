<?php

namespace Filament\Forms\Components\RichEditor;

use Illuminate\Contracts\Support\Arrayable;

class EditorCommand implements Arrayable
{
    /**
     * @param  array<mixed>  $arguments
     */
    public function __construct(
        readonly public string $name,
        readonly public array $arguments = [],
    ) {}

    /**
     * @return array{name: string, arguments: array<mixed>}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'arguments' => $this->arguments,
        ];
    }
}
