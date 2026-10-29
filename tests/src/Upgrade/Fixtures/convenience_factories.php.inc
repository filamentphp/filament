<?php

namespace Filament\Tests\Upgrade\Fixtures\ConvenienceFactories;

use Filament\Actions\{ButtonAction, IconButtonAction as IconAction};
use Filament\Tables\Filters\MultiSelectFilter as MultipleFilter;

return [
    ButtonAction::make('save')->label('Save changes'),
    IconAction::make(name: 'edit')->icon('heroicon-m-pencil'),
    \Filament\Actions\ButtonAction::make('view')->link(),
    IconAction::make('open')->button(),
    ButtonAction::make('custom')->view('custom-action'),
    MultipleFilter::make('status'),
    MultipleFilter::make(name: 'category')->options(['books' => 'Books', 'music' => 'Music'])->default(['music']),
    MultipleFilter::make('author')->relationship('author', 'name')->searchable()->preload()->label('Authors'),
    \Filament\Tables\Filters\MultiSelectFilter::make('rating')->native(false)->placeholder('Any rating'),
    MultipleFilter::make('tapped')->tap($configure),
    MultipleFilter::make('conditional')->when($condition, $configure),
    MultipleFilter::make('custom')->customConfiguration(),
    MultipleFilter::make('dynamic')->{$options}(false),
    MultipleFilter::make('nullsafe')?->{$options}(false),
    MultipleFilter::make('override')->multiple(false),
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\ConvenienceFactories;

use Filament\Actions\{ButtonAction, IconButtonAction as IconAction};
use Filament\Tables\Filters\MultiSelectFilter as MultipleFilter;

return [
    ButtonAction::make('save')->label('Save changes'),
    IconAction::make(name: 'edit')->icon('heroicon-m-pencil'),
    ButtonAction::make('view')->link(),
    IconAction::make('open')->button(),
    ButtonAction::make('custom')->view('custom-action'),
    MultipleFilter::make('status'),
    MultipleFilter::make(name: 'category')->options(['books' => 'Books', 'music' => 'Music'])->default(['music']),
    MultipleFilter::make('author')->relationship('author', 'name')->searchable()->preload()->label('Authors'),
    MultipleFilter::make('rating')->native(false)->placeholder('Any rating'),
    MultipleFilter::make('tapped')->tap($configure),
    MultipleFilter::make('conditional')->when($condition, $configure),
    MultipleFilter::make('custom')->customConfiguration(),
    MultipleFilter::make('dynamic')->{$options}(false),
    MultipleFilter::make('nullsafe')?->{$options}(false),
    MultipleFilter::make('override')->multiple(false),
];
