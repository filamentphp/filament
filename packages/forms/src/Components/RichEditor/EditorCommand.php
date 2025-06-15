<?php

namespace Filament\Forms\Components\RichEditor;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, string | array<mixed>>
 */
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
     * @param  array<mixed>  $arguments
     */
    public static function make(string $name, array $arguments = []): static
    {
        return app(static::class, ['name' => $name, 'arguments' => $arguments]);
    }

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
