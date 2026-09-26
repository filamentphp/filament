<?php

namespace Filament\Actions\Testing;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;
use Illuminate\Testing\Assert;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportValidation\TestsValidation;

class TestableImport
{
    use TestsValidation;

    protected ?Validator $validator = null;

    protected ?string $rowFailureMessage = null;

    protected bool $hasCompleted = false;

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
        $this->rowFailureMessage = null;
        $this->hasCompleted = false;

        try {
            ($this->importer)($data);
            $this->hasCompleted = true;
        } catch (ValidationException $exception) {
            $this->validator = $exception->validator;
        } catch (RowImportFailedException $exception) {
            $this->rowFailureMessage = $exception->getMessage();
        }

        return $this;
    }

    public function assertImported(): static
    {
        $this->assertHasNoErrors();
        $this->assertHasNoRowFailure();

        Assert::assertTrue($this->hasCompleted, 'Cannot assert imported: the latest importer invocation has not completed without an exception.');
        Assert::assertNotNull($this->getRecord(), 'Expected the row to be imported, but it was skipped.');

        return $this;
    }

    public function assertSkipped(): static
    {
        $this->assertHasNoErrors();
        $this->assertHasNoRowFailure();

        Assert::assertTrue($this->hasCompleted, 'Cannot assert skipped: the latest importer invocation has not completed without an exception.');
        Assert::assertNull($this->getRecord(), 'Expected the row to be skipped, but it was imported.');

        return $this;
    }

    public function assertHasRowFailure(?string $message = null): static
    {
        Assert::assertNotNull($this->rowFailureMessage, 'Importer has no row failure.');

        if ($message !== null) {
            Assert::assertSame($message, $this->rowFailureMessage, 'Importer row failure message does not match.');
        }

        return $this;
    }

    public function assertHasNoRowFailure(): static
    {
        Assert::assertNull($this->rowFailureMessage, "Importer has a row failure: [{$this->rowFailureMessage}].");

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
