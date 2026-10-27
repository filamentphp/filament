<?php

namespace Filament\Tests\Upgrade\Fixtures\LegacyFormManualBoundaries;

use Filament\Forms\Components\MultiSelect;
use Filament\Forms\Components\RelationshipRepeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;

trait CustomContent
{
    public function content(mixed $content): static
    {
        return parent::content($content);
    }
}

class CustomNotice extends Placeholder
{
    use CustomContent;

    public function state(mixed $state): static
    {
        return parent::state($state);
    }
}

class IntermediateNotice extends CustomNotice {}

class CustomSelect extends MultiSelect
{
    public function isMultiple(): bool
    {
        return parent::isMultiple();
    }
}

class CustomRepeater extends RelationshipRepeater
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->relationship(name: 'custom');
    }
}

/** @param array<Placeholder> $notices */
function configure(MultiSelect|Select $select, RelationshipRepeater $repeater, array $notices): Placeholder|TextEntry
{
    $select->multiple(false);
    $repeater->relationship(name: 'custom');

    return $notices[0]->content(content: 'Notice');
}

$select = MultiSelect::make('later');
$select->multiple(false);
$notice = Placeholder::make('later');
$notice->content(content: 'Later');
$notice->getContent();
$repeater = RelationshipRepeater::make('later');
$repeater->relationship(name: 'custom');
$reference =& $select;

return [
    MultiSelect::make('tags')->multiple(false)->label('Forced'),
    MultiSelect::make('tags')->multiple(condition: fn () => false),
    MultiSelect::make('tags')->multiple(),
    MultiSelect::make('tags')->tap($configure),
    MultiSelect::make('tags')->options(fn (MultiSelect $component) => []),
    MultiSelect::make('tags')->options(function (Select $component): array {
        $component->multiple(false);

        return ['a' => 'Alpha'];
    }),
    MultiSelect::make('tags')->options(fn (Select $component): array => $component?->multiple(condition: false) ? ['b' => 'Beta'] : []),
    RelationshipRepeater::make('items')->relationship(name: 'custom'),
    RelationshipRepeater::make('items')->relationship(modifyQueryUsing: fn ($query) => $query->where('active', true)),
    RelationshipRepeater::make('items')->schema(fn (RelationshipRepeater $component) => []),
    Placeholder::make('notice')->content(content: fn (Placeholder $component) => 'Typed'),
    Placeholder::make('notice')->content(fn ($component) => $component->getContent()),
    Placeholder::make('notice')->content(...$arguments),
    Placeholder::make('notice')->content('Content', sideEffect()),
    Placeholder::make('notice')->getContent(sideEffect()),
    Placeholder::make('notice')->getContent()->content('Not a component'),
    Placeholder::make('notice')->getContent(...),
    Placeholder::make('notice')->content(...),
    Placeholder::make('notice')?->content('Nullsafe'),
    Placeholder::make('notice')->{$method}('Dynamic'),
    Placeholder::make('notice')->customMethod(),
    CustomSelect::make('tags'),
    CustomRepeater::make('items'),
    IntermediateNotice::make('notice')->content('Custom'),
    MultiSelect::make(...),
    RelationshipRepeater::{$factory}('items'),
    new Placeholder('notice'),
    MultiSelect::class,
    $notice instanceof Placeholder,
    'Filament\\Forms\\Components\\Placeholder',
    $class::make('notice'),
    TextEntry::make('entry')->content(content: 'Not the legacy class')->getContent(),
];
