<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyFormFactories;

use Filament\Forms\Components\{MultiSelect as Multiple, RelationshipRepeater, Placeholder as Notice};
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

TextInput::configureUsing(fn (TextInput $component) => $component->required());

$select = Multiple::make('later');
$select->multiple(false);
$notice = Notice::make('later');
$notice->content(content: 'Later');
$notice->getContent();
$notice->content('Chained')->getContent();
$repeater = RelationshipRepeater::make('later');
$repeater->relationship(name: 'custom');
$reference =& $select;

$components = [
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
    Multiple::make('tags')->multiple(false)->label('Forced'),
    Multiple::make('tags')->multiple(condition: fn () => false),
    Multiple::make('tags')->multiple()->multiple(false),
    Multiple::make('tags')->validationMessages(['required' => 'Required'])->live()->maxItems(3),
    RelationshipRepeater::make('items')->relationship(name: 'custom'),
    RelationshipRepeater::make('items')->relationship(modifyQueryUsing: fn ($query) => $query->where('active', true)),
    RelationshipRepeater::make('items')->schema([Multiple::make('tags')->options([]), Notice::make('notice')->content('Nested')]),
    Multiple::make('tags')->tap(fn (Select $component) => $component->label('Tags'))->required(),
    Notice::make('notice')->when($condition, fn ($component) => $component->label('Notice'))->content('Content'),
    RelationshipRepeater::make('items')->unless($condition, fn ($component) => $component->collapsed()),
];

return $components;
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyFormFactories;

use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\{MultiSelect as Multiple, RelationshipRepeater, Placeholder as Notice};
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;

TextInput::configureUsing(fn (TextInput $component) => $component->required());

$select = Select::make('later')->multiple();
$select->multiple(false)->multiple();
$notice = TextEntry::make('later');
$notice->state(state: 'Later');
$notice->getState();
$notice->state('Chained')->getState();
$repeater = Repeater::make('later')->relationship();
$repeater->relationship(name: 'custom');
$reference =& $select;

$components = [
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
    Select::make('tags')->multiple()->multiple(false)->multiple()->label('Forced'),
    Select::make('tags')->multiple()->multiple(condition: fn () => false)->multiple(),
    Select::make('tags')->multiple()->multiple()->multiple()->multiple(false)->multiple(),
    Select::make('tags')->multiple()->validationMessages(['required' => 'Required'])->live()->maxItems(3),
    Repeater::make('items')->relationship()->relationship(name: 'custom'),
    Repeater::make('items')->relationship()->relationship(modifyQueryUsing: fn ($query) => $query->where('active', true)),
    Repeater::make('items')->relationship()->schema([Select::make('tags')->multiple()->options([]), TextEntry::make('notice')->state('Nested')]),
    Select::make('tags')->multiple()->tap(fn (Select $component) => $component->label('Tags'))->required(),
    TextEntry::make('notice')->when($condition, fn ($component) => $component->label('Notice'))->state('Content'),
    Repeater::make('items')->relationship()->unless($condition, fn ($component) => $component->collapsed()),
];

return $components;
