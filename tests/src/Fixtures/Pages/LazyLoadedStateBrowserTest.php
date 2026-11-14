<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Components\Attributes\Exposed;
use Filament\Support\State\LazyLoadedStateGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Renderless;
use Livewire\Attributes\Url;

class LazyLoadedStateBrowserTest extends Page implements HasTable
{
    use InteractsWithTable;

    protected static bool $shouldRegisterNavigation = false;

    #[Locked]
    public int $revision = 1;

    public string $note = 'Original note';

    #[Url]
    public bool $viewport = false;

    public bool $showViewportValues = true;

    public static int $groupLoads = 0;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refreshState')
                ->action(function (): void {
                    $this->revision++;
                }),
            Action::make('advanceState')->action('advanceState'),
            Action::make('toggleValues')
                ->visible(fn (): bool => $this->viewport)
                ->action(function (): void {
                    $this->showViewportValues = ! $this->showViewportValues;
                }),
        ];
    }

    #[Exposed]
    #[Renderless]
    public function advanceState(): void
    {
        $this->revision++;
        $this->dispatch('lazy-loaded-state-advanced', revision: $this->revision);
    }

    public function table(Table $table): Table
    {
        $columnGroup = LazyLoadedStateGroup::make('column')->state(function (array $records): array {
            static::$groupLoads++;

            return array_map(fn (array $record): string => "Column {$record['name']} / " . count($records), $records);
        });

        $rowGroup = LazyLoadedStateGroup::make(static fn (array $record): string => "row-{$record['id']}")
            ->state(function (array $record): array {
                static::$groupLoads++;

                return ['first' => "First {$record['name']}", 'second' => "Second {$record['name']}"];
            });

        return $table
            ->lazyLoaded($this->viewport)
            ->records(static fn (): array => [
                11 => ['id' => 11, 'name' => 'Alpha'],
                37 => ['id' => 37, 'name' => 'Beta'],
            ])
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('individual')
                    ->state(function (array $record): string {
                        $this->dispatch('lazy-loaded-state-resolved', name: $record['name'], revision: $this->revision);

                        return "Individual {$record['name']} v{$this->revision}";
                    })
                    ->lazyLoadedState(),
                TextColumn::make('column')
                    ->lazyLoadedState(group: $columnGroup, key: static fn (array $record): string => (string) $record['id']),
                TextColumn::make('first')->lazyLoadedState(group: $rowGroup),
                TextColumn::make('second')->lazyLoadedState(group: $rowGroup),
                TextColumn::make('private')->hidden()->state('Private column')->lazyLoadedState(),
            ]);
    }

    public function infolist(Schema $schema): Schema
    {
        $group = LazyLoadedStateGroup::make('entries')->state(function (): array {
            static::$groupLoads++;

            return ['total' => 0, 'missing' => null];
        });

        return $schema->components([
            TextEntry::make('independent')
                ->state(fn (): string => "Independent entry v{$this->revision}")
                ->lazyLoadedState(),
            TextEntry::make('total')->lazyLoadedState(group: $group)->formatStateUsing(static fn (int $state): string => "Total: {$state}"),
            TextEntry::make('missing')->lazyLoadedState(group: $group)->default('No result'),
            TextEntry::make('eager')->state(fn (): string => "Eager entry: {$this->note}"),
            TextEntry::make('private')->hidden()->state('Private entry')->lazyLoadedState(),
        ]);
    }

    public function content(Schema $schema): Schema
    {
        if ($this->viewport) {
            $group = LazyLoadedStateGroup::make('viewport')->state(fn (array $components): array => [
                'first' => 'First grouped value / ' . count($components) . " v{$this->revision}",
                'last' => 'Last grouped value / ' . count($components) . " v{$this->revision}",
            ]);

            return $schema->components([
                Text::make('Viewport controls are ready'),
                Section::make('Values')
                    ->key('viewportValues')
                    ->collapsed()
                    ->visible(fn (): bool => $this->showViewportValues)
                    ->extraAttributes(['data-testid' => 'viewport-values'])
                    ->schema([
                        TextEntry::make('firstIndependent')
                            ->state(fn (): string => "First independent value v{$this->revision}")
                            ->lazyLoadedState(),
                        TextEntry::make('first')->lazyLoadedState(group: $group),
                        Text::make('Scroll to the remaining values')
                            ->extraAttributes(['style' => 'display: block; height: 110vh']),
                        TextEntry::make('last')->lazyLoadedState(group: $group)
                            ->extraEntryWrapperAttributes(['data-testid' => 'last-grouped-value']),
                        TextEntry::make('lastIndependent')
                            ->state('Last independent value')
                            ->lazyLoadedState()
                            ->extraEntryWrapperAttributes(['data-testid' => 'last-independent-value']),
                    ]),
                Section::make('Catalogue')
                    ->key('viewportCatalogue')
                    ->collapsed()
                    ->extraAttributes(['data-testid' => 'viewport-catalogue'])
                    ->schema([EmbeddedTable::make()]),
            ]);
        }

        return $schema->components([
            TextInput::make('note')->extraInputAttributes(['data-testid' => 'lazy-loaded-state-note']),
            EmbeddedSchema::make('infolist'),
            EmbeddedTable::make(),
        ]);
    }
}
