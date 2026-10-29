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

use Filament\Forms\Components\{MultiSelect as Multiple, RelationshipRepeater, Placeholder as Notice};
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

return [
    Multiple::make(name: fieldName())->label(label())->options(['a' => 'Alpha', 'b' => 'Beta'])->default(['b']),
    Multiple::make('tags')->searchable()->preload()->native(false),
    Multiple::make('authors')->relationship(name: 'authors', titleAttribute: 'name'),
    RelationshipRepeater::make(name: 'comments')->schema([TextInput::make('body')])->orderColumn('position')->reorderable(),
    RelationshipRepeater::make('comments')->dehydrated()->defaultItems(2),
    Notice::make(name: 'notice')->content(content: fn (Get $get) => $get('title'))->label('Notice'),
    Notice::make('empty'),
    Notice::make('null')->content(null),
    Notice::make('empty-string')->content(''),
    Notice::make('notice')->content(firstContent())->state(secondContent())->content(content: lastContent())->getContent(),
    Notice::make('notice')->getContent(),
    Select::make('single')->multiple(false),
    $unrelated->content(content: 'Unchanged')->getContent(),
];
