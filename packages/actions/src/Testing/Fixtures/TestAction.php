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

    protected mixed $table = null;

    protected bool $isBulk = false;

    final public function __construct(
        protected string $name,
    ) {}

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

    public function table(mixed $recordKey = true): static
    {
        $this->table = $recordKey;

        return $this;
    }

    public function bulk(bool $condition = true): static
    {
        $this->isBulk = $condition;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $array = [
            'name' => $this->name,
            'arguments' => $this->arguments,
            'context' => [
                ...($this->isBulk ? ['bulk' => true] : []),
                ...($this->schemaComponent ? ['schemaComponent' => $this->schemaComponent] : []),
                ...$this->context,
            ],
        ];

        if (blank($this->table) || ($this->table === false)) {
            return $array;
        }

        $array['context']['table'] = true;

        if ($this->table === true) {
            return $array;
        }

        $array['context']['recordKey'] = $this->table;

        return $array;
    }
}
