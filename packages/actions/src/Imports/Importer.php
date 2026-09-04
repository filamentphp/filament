<?php

namespace Filament\Actions\Imports;

use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\Imports\Downloaders\Contracts\Downloader;
use Filament\Actions\Imports\Downloaders\CsvDownloader;
use Filament\Actions\Imports\Models\Import;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Filament\Support\Concerns\CanCallHooks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

abstract class Importer
{
    use CanCallHooks;

    // Security: Imports do not perform per-record authorization checks.
    // Each CSV row is processed by `resolveRecord()`, `fillRecord()`,
    // and `saveRecord()` without consulting Laravel policies. Add
    // manual checks in lifecycle hooks (`beforeCreate()`, etc.)
    // if needed. Failure CSVs contain original uploaded data, so
    // formula injection is neutralized by default when they are
    // generated — call `preventFormulaInjection(false)` to opt out.

    /** @var array<ImportColumn> */
    protected array $cachedColumns;

    /**
     * @var array<string, mixed>
     */
    protected array $originalData;

    /**
     * @var array<string, mixed>
     */
    protected array $data;

    protected ?Model $record;

    /**
     * @var class-string<Model>|null
     */
    protected static ?string $model = null;

    protected static bool $shouldPreventFormulaInjection = true;

    /**
     * @param  array<string, string>  $columnMap
     * @param  array<string, mixed>  $options
     */
    public function __construct(
        protected Import $import,
        protected array $columnMap,
        protected array $options,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function __invoke(array $data): void
    {
        $this->originalData = $this->data = $data;
        $this->record = null;

        $this->remapData();
        $this->castData();

        $this->record = $this->resolveRecord();

        if (! $this->record) {
            return;
        }

        $recordExists = $this->record->exists;

        if (! $recordExists) {
            $this->checkColumnMappingRequirementsForNewRecords();
        }

        $this->callHook('beforeValidate');
        $this->validateData();
        $this->callHook('afterValidate');

        $this->callHook('beforeFill');
        $this->fillRecord();
        $this->callHook('afterFill');

        $this->callHook('beforeSave');
        $this->callHook($recordExists ? 'beforeUpdate' : 'beforeCreate');
        $this->saveRecord();
        $this->callHook('afterSave');
        $this->callHook($recordExists ? 'afterUpdate' : 'afterCreate');
    }

    public function remapData(): void
    {
        $data = $this->data;

        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (blank($this->columnMap[$columnName] ?? null)) {
                continue;
            }

            $rowColumnName = $this->columnMap[$columnName];

            if (! array_key_exists($rowColumnName, $this->data)) {
                continue;
            }

            $data[$columnName] = $this->data[$rowColumnName];
        }

        $this->data = $data;
    }

    /**
     * @throws ValidationException
     */
    public function checkColumnMappingRequirementsForNewRecords(): void
    {
        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (filled($this->columnMap[$columnName] ?? null)) {
                continue;
            }

            if (! $column->isMappingRequiredForNewRecordsOnly()) {
                continue;
            }

            Validator::validate(
                data: [$columnName => null],
                rules: [$columnName => ['required']],
                messages: ["{$columnName}.required" => __('filament-actions::import.failure_csv.column_mapping_required_for_new_record')],
                attributes: [$columnName => $column->getLabel()],
            );
        }
    }

    public function castData(): void
    {
        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (! array_key_exists($columnName, $this->data)) {
                continue;
            }

            $this->data[$columnName] = $column->castState($this->data[$columnName]);
        }
    }

    public function resolveRecord(): ?Model
    {
        // Security: This method runs without policy checks.
        // Override to add authorization logic if needed.

        $keyName = app(static::getModel())->getKeyName();
        $keyColumnName = $this->columnMap[$keyName] ?? $keyName;

        return static::getModel()::find($this->data[$keyColumnName]);
    }

    /**
     * @throws ValidationException
     */
    public function validateData(): void
    {
        Validator::validate(
            $this->data,
            $this->getValidationRules(),
            $this->getValidationMessages(),
            $this->getValidationAttributes(),
        );
    }

    /**
     * @return array<string, array<mixed>>
     */
    public function getValidationRules(): array
    {
        $rules = [];

        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (blank($this->columnMap[$columnName] ?? null)) {
                continue;
            }

            $rules[$columnName] = $column->getDataValidationRules();

            if (
                $column->isMultiple() &&
                count($nestedRecursiveRules = $column->getNestedRecursiveDataValidationRules())
            ) {
                $rules["{$columnName}.*"] = $nestedRecursiveRules;
            }
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function getValidationMessages(): array
    {
        return [];
    }

    /**
     * @return array<string, string>
     */
    public function getValidationAttributes(): array
    {
        $attributes = [];

        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (blank($this->columnMap[$columnName] ?? null)) {
                continue;
            }

            $validationAttribute = $column->getValidationAttribute();

            if (blank($validationAttribute)) {
                continue;
            }

            $attributes[$columnName] = $validationAttribute;
        }

        return $attributes;
    }

    public function fillRecord(): void
    {
        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (blank($this->columnMap[$columnName] ?? null)) {
                continue;
            }

            if (! array_key_exists($columnName, $this->data)) {
                continue;
            }

            $state = $this->data[$columnName];

            if (blank($state) && $column->isBlankStateIgnored()) {
                continue;
            }

            $column->fillRecord($state);
        }
    }

    public function saveRecord(): void
    {
        $this->record->save();

        foreach ($this->getCachedColumns() as $column) {
            $columnName = $column->getName();

            if (blank($this->columnMap[$columnName] ?? null)) {
                continue;
            }

            if (! array_key_exists($columnName, $this->data)) {
                continue;
            }

            $state = $this->data[$columnName];

            if (blank($state) && $column->isBlankStateIgnored()) {
                continue;
            }

            $column->saveRelationships($state);
        }
    }

    /**
     * @return array<ImportColumn>
     */
    abstract public static function getColumns(): array;

    /**
     * @return array<Component | Action | ActionGroup>
     */
    public static function getOptionsFormComponents(): array
    {
        return [];
    }

    /**
     * @return class-string<Model>
     */
    public static function getModel(): string
    {
        return static::$model ?? (string) str(class_basename(static::class))
            ->beforeLast('Importer')
            ->prepend(app()->getNamespace() . 'Models\\');
    }

    public static function preventFormulaInjection(bool $condition = true): void
    {
        static::$shouldPreventFormulaInjection = $condition;
    }

    public static function shouldPreventFormulaInjection(): bool
    {
        // Security: On by default to neutralize CSV formula injection (CWE-1236)
        // in the downloadable failure CSV, which admins open in spreadsheet
        // software. Values that begin with a formula-triggering character are
        // prefixed with a `'`; purely numeric strings such as `-5` are left
        // unchanged so the failure CSV can be corrected and re-uploaded without
        // corrupting legitimate data. The failure CSV includes every uploaded
        // column, even those not mapped to an `ImportColumn`, so this is a
        // whole-file toggle rather than a per-column one. Disable it for a
        // single importer by redeclaring `$shouldPreventFormulaInjection`, or
        // globally by calling `Importer::preventFormulaInjection(false)` in a
        // service provider.
        return static::$shouldPreventFormulaInjection;
    }

    public static function getFailedRowsDownloader(): Downloader
    {
        return app(CsvDownloader::class);
    }

    abstract public static function getCompletedNotificationBody(Import $import): string;

    public static function getCompletedNotificationTitle(Import $import): string
    {
        return __('filament-actions::import.notifications.completed.title');
    }

    public static function modifyCompletedNotification(Notification $notification, Import $import): Notification
    {
        return $notification;
    }

    /**
     * @return array<int, object>
     */
    public function getJobMiddleware(): array
    {
        return [
            (new WithoutOverlapping("import{$this->import->getKey()}"))->expireAfter(600),
        ];
    }

    public function getJobRetryUntil(): ?CarbonInterface
    {
        return now()->addDay();
    }

    /**
     * @return int | array<int> | null
     */
    public function getJobBackoff(): int | array | null
    {
        return [60, 120, 300, 600];
    }

    /**
     * @return array<int, string>
     */
    public function getJobTags(): array
    {
        return ["import{$this->import->getKey()}"];
    }

    public function getJobQueue(): ?string
    {
        return null;
    }

    public function getJobConnection(): ?string
    {
        return null;
    }

    public function getJobBatchName(): ?string
    {
        return null;
    }

    /**
     * @return array<ImportColumn>
     */
    public function getCachedColumns(): array
    {
        return $this->cachedColumns ??= array_map(
            fn (ImportColumn $column) => $column->importer($this),
            static::getColumns(),
        );
    }

    public function getRecord(): ?Model
    {
        return $this->record;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOriginalData(): array
    {
        return $this->originalData;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getImport(): Import
    {
        return $this->import;
    }
}
