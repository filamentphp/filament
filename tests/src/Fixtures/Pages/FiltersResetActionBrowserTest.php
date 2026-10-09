<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tests\Fixtures\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class FiltersResetActionBrowserTest extends Page implements HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public string $focusScenario = 'default';

    public bool $hasFocusLifecycleFilters = false;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedFunnel;

    protected static bool $shouldRegisterNavigation = false;

    public function mount(): void
    {
        $this->focusScenario = request()->string('focusScenario')->toString();
        $this->hasFocusLifecycleFilters = request()->boolean('focus');
    }

    public function table(Table $table): Table
    {
        $publishedFilter = Tables\Filters\Filter::make('is_published')
            ->query(static fn (Builder $query): Builder => $query->where('is_published', true))
            ->schema([
                Toggle::make('isActive')
                    ->label('Published')
                    ->extraAttributes(['data-testid' => 'published-filter']),
            ]);

        $detailsFilter = Tables\Filters\Filter::make('details')
            ->schema([
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->native(false)
                    ->searchable()
                    ->extraAttributes(['data-testid' => 'status-filter']),
                DatePicker::make('published_at')
                    ->native(false)
                    ->extraAttributes(['data-testid' => 'published-at-filter']),
                ColorPicker::make('color')
                    ->extraAttributes(['data-testid' => 'color-filter']),
            ]);

        $dateFilter = Tables\Filters\Filter::make('date')
            ->schema([
                DatePicker::make('published_at')
                    ->native(false)
                    ->extraAttributes(['data-testid' => 'published-at-filter']),
            ]);

        return $table
            ->query(Post::query())
            ->columns([
                Tables\Columns\TextColumn::make('title'),
            ])
            ->filters(match ($this->focusScenario) {
                'disabled' => [
                    Tables\Filters\Filter::make('disabled')
                        ->schema([
                            Select::make('value')
                                ->native(false)
                                ->disabled(),
                        ]),
                ],
                'disabledFirst' => [
                    Tables\Filters\Filter::make('disabled')
                        ->schema([
                            TextInput::make('value')
                                ->disabled()
                                ->extraInputAttributes(['tabindex' => 0]),
                        ]),
                    $publishedFilter,
                ],
                'hiddenFirst' => [
                    Tables\Filters\Filter::make('hidden')
                        ->schema([
                            TextInput::make('value')
                                ->extraInputAttributes(['style' => 'visibility: hidden']),
                        ]),
                    $publishedFilter,
                ],
                'dateFirst' => [$dateFilter, $publishedFilter],
                'empty' => [Tables\Filters\Filter::make('empty')->schema([])],
                'selectFirst' => [$detailsFilter, $publishedFilter],
                default => $this->hasFocusLifecycleFilters ? [$publishedFilter, $detailsFilter] : [$publishedFilter],
            })
            ->deferFilters(false)
            ->filtersTriggerAction(
                static fn (Action $action) => $action
                    ->extraAttributes(['data-testid' => 'filters-trigger']),
            )
            ->filtersResetAction(
                static fn (Action $action) => $action
                    ->label('Reset filters')
                    ->icon(Heroicon::XMark)
                    ->iconButton()
                    ->extraAttributes(['data-testid' => 'filters-reset-action']),
            );
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([EmbeddedTable::make()]);
    }
}
