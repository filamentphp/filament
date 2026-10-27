<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyFormFactories;

use Filament\Forms\Components\{MultiSelect as Multiple, RelationshipRepeater, Placeholder as Notice};
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

return [
    Multiple::make(name: fieldName())->label(label())->options(['a' => 'Alpha', 'b' => 'Beta'])->default(['b']),
    \Filament\Forms\Components\MultiSelect::make('tags')->searchable()->preload()->native(false),
    Multiple::make('authors')->relationship(name: 'authors', titleAttribute: 'name'),
    RelationshipRepeater::make(name: 'comments')->schema([TextInput::make('body')])->orderColumn('position')->reorderable(),
    \Filament\Forms\Components\RelationshipRepeater::make('comments')->dehydrated()->defaultItems(2),
    Notice::make(name: 'notice')->content(content: fn (Get $get) => $get('title'))->label('Notice'),
    Notice::make('empty'),
    Notice::make('null')->content(null),
    Notice::make('empty-string')->content(''),
    Notice::make('notice')->content(firstContent())->state(secondContent())->content(content: lastContent())->getContent(),
    \Filament\Forms\Components\Placeholder::make('notice')->getContent(),
    Select::make('single')->multiple(false),
    $unrelated->content(content: 'Unchanged')->getContent(),
];
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyFormFactories;

use Filament\Forms\Components\Repeater;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\{MultiSelect as Multiple, RelationshipRepeater, Placeholder as Notice};
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

return [
    Select::make(name: fieldName())->multiple()->label(label())->options(['a' => 'Alpha', 'b' => 'Beta'])->default(['b']),
    Select::make('tags')->multiple()->searchable()->preload()->native(false),
    Select::make('authors')->multiple()->relationship(name: 'authors', titleAttribute: 'name'),
    Repeater::make(name: 'comments')->relationship()->schema([TextInput::make('body')])->orderColumn('position')->reorderable(),
    Repeater::make('comments')->relationship()->dehydrated()->defaultItems(2),
    TextEntry::make(name: 'notice')->state(state: fn (Get $get) => $get('title'))->label('Notice'),
    TextEntry::make('empty'),
    TextEntry::make('null')->state(null),
    TextEntry::make('empty-string')->state(''),
    TextEntry::make('notice')->state(firstContent())->state(secondContent())->state(state: lastContent())->getState(),
    TextEntry::make('notice')->getState(),
    Select::make('single')->multiple(false),
    $unrelated->content(content: 'Unchanged')->getContent(),
];
