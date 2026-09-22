<?php

namespace Filament\Actions\Testing;

use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportValidation\TestsValidation;

class TestImporter
{
    use TestsValidation;

    protected ?Validator $validator = null;

    final public function __construct(
        protected Importer $importer,
    ) {}

    /**
     * @param  class-string<Importer>  $importer
     * @param  array<string, string> | null  $columnMap
     * @param  array<string, mixed>  $options
     */
    public static function make(string $importer, ?array $columnMap = null, array $options = [], ?Import $import = null): static
    {
        $import ??= app(Import::class);
        $import->importer = $importer;

        if ($columnMap === null) {
            $columnMap = [];

            foreach ($importer::getColumns() as $column) {
                $columnMap[$column->getName()] = $column->getName();
            }
        }

        return app(static::class, [
            'importer' => $import->getImporter($columnMap, $options),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function import(array $data): static
    {
        $this->validator = null;

        try {
            ($this->importer)($data);
        } catch (ValidationException $exception) {
            $this->validator = $exception->validator;
        }

        return $this;
    }

    public function errors(): MessageBag
    {
        return $this->validator?->errors() ?? new MessageBag;
    }

    /**
     * @return array<string, array<string, array<mixed>>>
     */
    public function failedRules(): array
    {
        return $this->validator?->failed() ?? [];
    }

    public function getRecord(): ?Model
    {
        return $this->importer->getRecord();
    }
}
