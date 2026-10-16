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

use Filament\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\{ButtonAction, IconButtonAction as IconAction};
use Filament\Tables\Filters\MultiSelectFilter as MultipleFilter;

return [
    Action::make('save')->button()->label('Save changes'),
    Action::make(name: 'edit')->iconButton()->icon('heroicon-m-pencil'),
    Action::make('view')->button()->link(),
    Action::make('open')->iconButton()->button(),
    Action::make('custom')->button()->view('custom-action'),
    SelectFilter::make('status')->multiple(),
    SelectFilter::make(name: 'category')->multiple()->options(['books' => 'Books', 'music' => 'Music'])->default(['music']),
    SelectFilter::make('author')->multiple()->relationship('author', 'name')->searchable()->preload()->label('Authors'),
    SelectFilter::make('rating')->multiple()->native(false)->placeholder('Any rating'),
    SelectFilter::make('tapped')->multiple()->tap($configure),
    SelectFilter::make('conditional')->multiple()->when($condition, $configure),
    SelectFilter::make('custom')->multiple()->customConfiguration(),
    SelectFilter::make('dynamic')->multiple()->{$options}(false),
    SelectFilter::make('nullsafe')->multiple()?->{$options}(false),
    SelectFilter::make('override')->multiple()->multiple(false),
];
