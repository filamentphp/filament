<?php

namespace Filament\Actions\Testing;

use Closure;
use Filament\Support\ArrayRecord;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class TestAction implements Arrayable
{
    /** @var array<string, mixed> | Closure | null */
    protected array | Closure | null $arguments = null;

    /** @var array<string, mixed> */
    protected array $context = [];

    protected ?string $schemaComponent = null;

    protected ?string $schema = null;

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
     * @param  array<string, mixed> | Closure | null  $arguments
     */
    public function arguments(array | Closure | null $arguments): static
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function schemaComponent(?string $component, ?string $schema = null): static
    {
        $this->schemaComponent = $component;
        $this->schema = $schema;

        return $this;
    }

    public function table(mixed $record = true): static
    {
        $this->table = $record;

        return $this;
    }

    public function bulk(bool $condition = true): static
    {
        $this->isBulk = $condition;

        return $this;
    }

    /**
     * @param  array<string, mixed>  $actualArguments
     */
    public function checkArguments(array $actualArguments): bool
    {
        if (! ($this->arguments instanceof Closure)) {
            return true;
        }

        return ($this->arguments)($actualArguments);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(?string $defaultSchema = null): array
    {
        $schema = $this->schema ?? $defaultSchema;

        $array = [
            'name' => $this->name,
            ...((is_array($this->arguments)) ? ['arguments' => $this->arguments] : []),
            'context' => [
                ...($this->isBulk ? ['bulk' => true] : []),
                ...($this->schemaComponent ? ['schemaComponent' => (filled($schema) ? "{$schema}." : '') . $this->schemaComponent] : []),
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

        if ($this->table instanceof Model) {
            $array['context']['recordKey'] = $this->table->getKey();
        } elseif (is_array($this->table)) {
            $array['context']['recordKey'] = $this->table[ArrayRecord::getKeyName()] ?? null;
        } else {
            $array['context']['recordKey'] = $this->table;
        }

        return $array;
    }
}
