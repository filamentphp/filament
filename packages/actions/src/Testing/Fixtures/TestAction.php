<?php

namespace Filament\Actions\Testing\Fixtures;

use Illuminate\Contracts\Support\Arrayable;

class TestAction implements Arrayable
{
    /** @var array<string, mixed> */
    protected array $arguments = [];

    /** @var array<string, mixed> */
    protected array $context = [];

    protected ?string $schemaComponent = null;

    final public function __construct(
        protected string $name,
    ) {
    }

    public static function make(string $name): static
    {
        return app(static::class, ['name' => $name]);
    }

    /**
     * @param  array<string, mixed>  $arguments
     */
    public function arguments(array $arguments): static
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function schemaComponent(?string $key): static
    {
        $this->schemaComponent = $key;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'arguments' => $this->arguments,
            'context' => array_merge(
                ...($this->schemaComponent ? ['schemaComponent' => $this->schemaComponent] : []),
                ...$this->context,
            ),
        ];
    }
}
