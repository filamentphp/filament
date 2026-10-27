<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaConfigurationCalls;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

function configure(Action $action, CreateAction $create, Select $select, ?Section $section, Get $get, Set $set, Schema $schema): void
{
    $action->fillForm(data: ['title' => 'Example']);
    $action->beforeFormFilled(callback: null)->afterFormFilled(callback: null);
    $action->beforeFormValidated(callback: null)->afterFormValidated(callback: null);
    $action->callBeforeFormFilled();
    $action->callAfterFormFilled();
    $action->callBeforeFormValidated();
    $action->callAfterFormValidated();
    $create->preserveFormDataWhenCreatingAnother(fields: ['title']);
    $select->manageOptionForm(schema: [])->createOptionForm(schema: [])->editOptionForm(schema: null);
    $select->getCreateOptionActionForm(schema: $schema);
    $select->getEditOptionActionForm(schema: $schema);
    $select->hasCreateOptionActionFormSchema();
    $select->hasEditOptionActionFormSchema();
    $section?->formBefore(condition: false)->isFormBefore();
    $get->skipComponentsChildContainersWhileSearching(condition: false);
    $set->skipComponentsChildContainersWhileSearching(condition: true);
    $callback = $action->beforeFormFilled(...);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\SchemaConfigurationCalls;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

function configure(Action $action, CreateAction $create, Select $select, ?Section $section, Get $get, Set $set, Schema $schema): void
{
    $action->fillSchema(data: ['title' => 'Example']);
    $action->beforeSchemaFilled(callback: null)->afterSchemaFilled(callback: null);
    $action->beforeSchemaValidated(callback: null)->afterSchemaValidated(callback: null);
    $action->callBeforeSchemaFilled();
    $action->callAfterSchemaFilled();
    $action->callBeforeSchemaValidated();
    $action->callAfterSchemaValidated();
    $create->preserveDataWhenCreatingAnother(fields: ['title']);
    $select->manageOptionSchema(schema: [])->createOptionSchema(schema: [])->editOptionSchema(schema: null);
    $select->getCreateOptionActionSchema(schema: $schema);
    $select->getEditOptionActionSchema(schema: $schema);
    $select->hasCreateOptionActionSchema();
    $select->hasEditOptionActionSchema();
    $section?->contentBefore(condition: false)->isContentBefore();
    $get->skipComponentsChildSchemasWhileSearching(condition: false);
    $set->skipComponentsChildSchemasWhileSearching(condition: true);
    $callback = $action->beforeSchemaFilled(...);
}
