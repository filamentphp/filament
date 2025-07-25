<?php

namespace Filament\Actions;

use Closure;
use Filament\Actions\Concerns\CanCustomizeProcess;
use Filament\Actions\View\ActionsIconAlias;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentIcon;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;

class ReplicateAction extends Action
{
    use CanCustomizeProcess;

    protected ?Closure $beforeReplicaSaved = null;

    /**
     * @var array<string> | Closure | null
     */
    protected array | Closure | null $excludedAttributes = null;

    protected ?Model $replica = null;

    protected ?Closure $mutateRecordDataUsing = null;

    protected array $replicateFilesUsing = [];

    protected array $replicateFileNamesUsing = [];

    public static function getDefaultName(): ?string
    {
        return 'replicate';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-actions::replicate.single.label'));

        $this->modalHeading(fn (): string => __('filament-actions::replicate.single.modal.heading', ['label' => $this->getRecordTitle()]));

        $this->modalSubmitActionLabel(__('filament-actions::replicate.single.modal.actions.replicate.label'));

        $this->successNotificationTitle(__('filament-actions::replicate.single.notifications.replicated.title'));

        $this->fillForm(function (Model $record): array {
            $data = Arr::except($record->attributesToArray(), $this->getExcludedAttributes() ?? []);

            if ($this->mutateRecordDataUsing) {
                $data = $this->evaluate($this->mutateRecordDataUsing, ['data' => $data]);
            }

            return $data;
        });

        $this->action(function () {
            $result = $this->process(function (array $data, Model $record, ?Schema $schema): void {
                if ((! $schema) && blank($data) && $this->mutateRecordDataUsing) {
                    $data = $this->evaluate(
                        $this->mutateRecordDataUsing,
                        ['data' => Arr::except($record->attributesToArray(), $this->getExcludedAttributes() ?? [])],
                    );
                }

                $this->replica = $record->replicate($this->getExcludedAttributes());

                $this->replica->fill(array_merge(
                    $data,
                    $this->getReplicatedFiles(),
                    $this->getReplicatedFileNames(),
                ));

                $this->callBeforeReplicaSaved();

                $this->replica->save();
            });

            try {
                return $result;
            } finally {
                $this->success();
            }
        });

        $this->tableIcon(FilamentIcon::resolve(ActionsIconAlias::REPLICATE_ACTION) ?? Heroicon::Square2Stack);
        $this->groupedIcon(FilamentIcon::resolve(ActionsIconAlias::REPLICATE_ACTION_GROUPED) ?? Heroicon::Square2Stack);
    }

    public function beforeReplicaSaved(?Closure $callback): static
    {
        $this->beforeReplicaSaved = $callback;

        return $this;
    }

    public function mutateRecordDataUsing(?Closure $callback): static
    {
        $this->mutateRecordDataUsing = $callback;

        return $this;
    }

    /**
     * @deprecated Use `after()` instead.
     */
    public function afterReplicaSaved(?Closure $callback): static
    {
        $this->after($callback);

        return $this;
    }

    /**
     * @param  array<string> | Closure | null  $attributes
     */
    public function excludeAttributes(array | Closure | null $attributes): static
    {
        $this->excludedAttributes = $attributes;

        return $this;
    }

    public function callBeforeReplicaSaved(): void
    {
        $this->evaluate($this->beforeReplicaSaved);
    }

    /**
     * @return array<string> | null
     */
    public function getExcludedAttributes(): ?array
    {
        return $this->evaluate($this->excludedAttributes);
    }

    public function getReplica(): ?Model
    {
        return $this->replica;
    }

    public function copyFile(string $file, string | Closure $directory, string | Closure | null $disk = null): string
    {
        $fileName = basename($file);
        $directory = $this->evaluate($directory);
        $disk = $this->evaluate($disk) ?: config('filesystems.default', 'local');
        Storage::disk($disk)->copy($file, $path = "{$directory}/{$fileName}");
        return $path;
    }

    public function replicateFiles(string | Closure $column, string | Closure $directory, string | Closure | null $disk = null): static
    {
        $column = $this->evaluate($column);
        $this->replicateFilesUsing[] = fn (Model $record): array => [
            $column => is_iterable($record->{$column})
                ? collect($record->{$column})->map(fn (string $file): string => $this->copyFile($file, $directory, $disk))->toArray()
                : $this->copyFile($record->{$column}, $directory, $disk)
        ];
        return $this;
    }

    public function replicateFileNames(string | Closure $column, string | Closure $directory): static
    {
        $column = $this->evaluate($column);
        $directory = $this->evaluate($directory);
        $this->replicateFileNamesUsing[] = fn (Model $record): array => [
            $column => is_iterable($record->{$column})
                ? collect($record->{$column})->mapWithKeys(fn (string $name, string $file): array => [$directory.'/'.basename($file) => $name])->toArray()
                : $record->{$column}
        ];
        return $this;
    }

    public function getReplicatedFiles(): array
    {
        return collect($this->replicateFilesUsing)->mapWithKeys(fn (Closure $callback) => $this->evaluate($callback))->toArray();
    }

    public function getReplicatedFileNames(): array
    {
        return collect($this->replicateFileNamesUsing)->mapWithKeys(fn (Closure $callback) => $this->evaluate($callback))->toArray();
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        return match ($parameterName) {
            'replica' => [$this->getReplica()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }
}
