<?php

namespace Filament\Tests\Fixtures\Pages;

use Filament\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\State\LazyLoadedStateGroup;
use Filament\Tests\Fixtures\Models\Post;
use Livewire\Attributes\Url;

class LazyLoadedRepeatableEntryBrowserTest extends Page
{
    protected static bool $shouldRegisterNavigation = false;

    public bool $grouped = true;

    #[Url]
    public bool $lazyItemSchemas = false;

    public bool $hideOrders = false;

    public bool $emptyOrders = false;

    public bool $useDefaultOrders = false;

    public bool $useDefaultPackages = false;

    public int $revision = 1;

    public ?string $selectedParcel = null;

    public static int $parentLoads = 0;

    public static int $packageLoads = 0;

    public static int $unrelatedLoads = 0;

    public function infolist(Schema $schema): Schema
    {
        $ordersGroup = LazyLoadedStateGroup::make('orders')->state(function (): array {
            return ['orders' => $this->useDefaultOrders ? null : $this->loadOrders(), 'summary' => 'Two deliveries'];
        });

        $packageGroup = LazyLoadedStateGroup::make(static fn (Post $record): string => "packages-{$record->getKey()}")
            ->state(function (Post $record): array {
                static::$packageLoads++;

                return ['packages' => $this->useDefaultPackages ? [] : ['box' => ['reference' => "{$record->title} / box v{$this->revision}"]]];
            });

        return $schema->components([
            Section::make('Deliveries')
                ->key('deliveries')
                ->hidden(fn (): bool => $this->hideOrders)
                ->schema([
                    RepeatableEntry::make('orders')
                        ->default(fn (): array => $this->useDefaultOrders ? ['fallback' => (new Post)->forceFill(['id' => 83, 'title' => 'Fallback parcel'])] : [])
                        ->state(fn (): array => $this->loadOrders())
                        ->lazyLoadedState(group: $this->grouped ? $ordersGroup : null)
                        ->schema(Schema::make()->lazyLoaded(fn (): bool => $this->lazyItemSchemas)->components([
                            TextEntry::make('title'),
                            RepeatableEntry::make('packages')
                                ->default(['box' => ['reference' => 'Fallback box']])
                                ->lazyLoadedState(group: $packageGroup)
                                ->schema(Schema::make()->lazyLoaded(fn (): bool => $this->lazyItemSchemas)->components([
                                    TextEntry::make('reference')
                                        ->lazyLoadedState()
                                        ->hintAction(Action::make('selectParcel')
                                            ->label(static fn (Post $record): string => "Select {$record->title}")
                                            ->requiresConfirmation()
                                            ->action(function (Post $record): void {
                                                $this->selectedParcel = $record->title;
                                            })),
                                ])),
                        ])),
                    TextEntry::make('summary')->lazyLoadedState(group: $ordersGroup),
                ]),
            RepeatableEntry::make('unrelated')
                ->lazyLoadedState()
                ->state(function (): array {
                    static::$unrelatedLoads++;

                    return [];
                })
                ->schema([TextEntry::make('secret')]),
        ]);
    }

    protected function loadOrders(): array
    {
        static::$parentLoads++;

        return $this->emptyOrders ? [] : [
            17 => (new Post)->forceFill(['id' => 17, 'title' => 'Parcel 1048']),
            39 => (new Post)->forceFill(['id' => 39, 'title' => 'Parcel 1056']),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [Action::make('refreshParcels')->action(function (): void {
            $this->revision++;
        })];
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('selection')->state(fn (): string => $this->selectedParcel ?? 'No parcel selected'),
            EmbeddedSchema::make('infolist'),
        ]);
    }
}
