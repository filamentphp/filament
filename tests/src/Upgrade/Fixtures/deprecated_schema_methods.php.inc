<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedSchemaMethods;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget\Stat;

function configure(Schema $schema, Component $component, Step $step, ?Schema $nullableSchema, ?Section $section, TextInput $field, TextEntry $entry, Stat $stat, RichEditorTool $tool, array $arguments): void
{
    $schema->reactive();
    $component->reactive();
    $nullableSchema?->reactive();
    $field->reactive()->debounce(250);
    $entry->REACTIVE();
    TextInput::make('title')->reactive();
    $schema->name(name: fn (): string => 'details')->context(context: 'edit');
    $schema->getName();
    $schema->clearCachedDefaultChildSchemas();
    $schema->name(null)->context(null);
    $nullableSchema?->name(name: null)?->context(context: null);
    $nullableSchema?->getName();
    $component->getGetCallback();
    $component->getSetCallback();
    $component->getChildComponentContainer();
    $component->getChildComponentContainer(key: 'footer');
    $component->getChildComponentContainers();
    $component->getChildComponentContainers(withHidden: true);
    $component->clearCachedDefaultChildSchemas();
    $step->afterValidated(callback: fn () => null)->disableLabel(condition: false);
    $step->afterValidated(null)->disableLabel();
    $section?->disableLabel(condition: fn (): bool => true)?->getChildComponentContainers(withHidden: false);
    $field->disableLabel(false);
    $entry->disableLabel(condition: fn (): bool => false);
    $stat->disableLabel();
    $tool->disableLabel(condition: true);
    $component->getChildComponentContainer(...$arguments);
    $step->afterValidated(...$arguments);
    $field->disableLabel(...$arguments);
    $schema->NAME(name: 'other');
    $callback = $schema->name(...);
    $callback = $schema->context(...);
    $callback = $component->getGetCallback(...);
    $callback = $component->getSetCallback(...);
    $callback = $component->getChildComponentContainer(...);
    $callback = $component->getChildComponentContainers(...);
    $callback = $schema->clearCachedDefaultChildSchemas(...);
    $callback = $step->afterValidated(...);
    $callback = $field->disableLabel(...);
    Schema::make()->name('details')->context('create')->getName();
    Section::make('Details')->disableLabel()->getChildComponentContainer();
    TextInput::make('title')->disableLabel();
    $field->name('title')->getName();
    $entry->name('title')->getName();
}

class CustomSchema extends Schema
{
    public function configureSchema(): static
    {
        return $this->name(name: 'details')->context(context: 'edit');
    }
}

class CustomSection extends Section {}

function inherited(CustomSchema $schema, CustomSection $section): void
{
    $schema->statePath('data')->name(name: null)->context(context: null)->getName();
    $section->schema([])->disableLabel()->getChildComponentContainers();
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\DeprecatedSchemaMethods;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Schema;
use Filament\Widgets\StatsOverviewWidget\Stat;

function configure(Schema $schema, Component $component, Step $step, ?Schema $nullableSchema, ?Section $section, TextInput $field, TextEntry $entry, Stat $stat, RichEditorTool $tool, array $arguments): void
{
    $schema->live();
    $component->live();
    $nullableSchema?->live();
    $field->live()->debounce(250);
    $entry->live();
    TextInput::make('title')->live();
    $schema->key(key: fn (): string => 'details')->operation(operation: 'edit');
    $schema->getKey();
    $schema->clearCachedChildSchemas();
    $schema->key(null)->operation(null);
    $nullableSchema?->key(key: null)?->operation(operation: null);
    $nullableSchema?->getKey();
    $component->makeGetUtility();
    $component->makeSetUtility();
    $component->getChildSchema();
    $component->getChildSchema(key: 'footer');
    $component->getChildSchemas();
    $component->getChildSchemas(withHidden: true);
    $component->clearCachedChildSchemas();
    $step->afterValidation(callback: fn () => null)->hiddenLabel(condition: false);
    $step->afterValidation(null)->hiddenLabel();
    $section?->hiddenLabel(condition: fn (): bool => true)?->getChildSchemas(withHidden: false);
    $field->hiddenLabel(false);
    $entry->hiddenLabel(condition: fn (): bool => false);
    $stat->hiddenLabel();
    $tool->hiddenLabel(condition: true);
    $component->getChildSchema(...$arguments);
    $step->afterValidation(...$arguments);
    $field->hiddenLabel(...$arguments);
    $schema->key(key: 'other');
    $callback = $schema->key(...);
    $callback = $schema->operation(...);
    $callback = $component->makeGetUtility(...);
    $callback = $component->makeSetUtility(...);
    $callback = $component->getChildSchema(...);
    $callback = $component->getChildSchemas(...);
    $callback = $schema->clearCachedChildSchemas(...);
    $callback = $step->afterValidation(...);
    $callback = $field->hiddenLabel(...);
    Schema::make()->key('details')->operation('create')->getKey();
    Section::make('Details')->hiddenLabel()->getChildSchema();
    TextInput::make('title')->hiddenLabel();
    $field->name('title')->getName();
    $entry->name('title')->getName();
}

class CustomSchema extends Schema
{
    public function configureSchema(): static
    {
        return $this->key(key: 'details')->operation(operation: 'edit');
    }
}

class CustomSection extends Section {}

function inherited(CustomSchema $schema, CustomSection $section): void
{
    $schema->statePath('data')->key(key: null)->operation(operation: null)->getKey();
    $section->schema([])->hiddenLabel()->getChildSchemas();
}
