<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralConfiguration;

use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload as Upload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

function components(): array
{
    return [
        BaseFileUpload::make('file')->disablePreview(),
        Upload::make('photo')->disablePreview(condition: false)->imageCropAspectRatio(ratio: '16:9'),
        Upload::make('blank')->imageCropAspectRatio('   '),
        Upload::make('zero')->imageCropAspectRatio('0'),
        Upload::make('reset')->imageAspectRatio('4:3')->automaticallyCropImagesToAspectRatio()->imageCropAspectRatio(null),
        Builder::make('blocks')->disableItemCreation()->disableItemDeletion(false)->disableItemMovement(true)->inset(),
        Repeater::make('items')->disableItemCreation(false)->disableItemDeletion()->disableItemMovement(false)->inset(condition: false),
        KeyValue::make('metadata')->disableAddingRows()->disableDeletingRows(false)->disableEditingKeys(false)->disableEditingValues(),
        TextInput::make('name')->disableAutocomplete(false)->disableAutocapitalize(),
        TextInput::make('other')->disableAutocomplete()->disableAutocapitalize(false),
        Textarea::make('description')->disableAutocomplete(condition: false),
        Select::make('status')->disablePlaceholderSelection(),
        Select::make('other_status')->disablePlaceholderSelection(false),
        DateTimePicker::make('published')->withoutDate()->withoutSeconds(condition: true)->withoutTime(false)->icon(),
        DatePicker::make('date')->icon(icon: null),
        TimePicker::make('time')->icon('heroicon-o-clock'),
        DateTimePicker::make('unchanged')->suffixIcon('heroicon-o-star', isInline: false)->icon(false),
        Action::make('start')->modalAlignment(Alignment::Start)->centerModal(false),
        Action::make('end')->modalAlignment(Alignment::End)->centerModal(null),
        Action::make('center')->modalAlignment(Alignment::End)->centerModal(condition: true),
        CreateAction::make()->disableCreateAnother(),
        AttachAction::make()->disableAttachAnother(false),
        AssociateAction::make()->disableAssociateAnother(true),
        Upload::make('ordered')->imageCropAspectRatio('16:9')->automaticallyCropImagesToAspectRatio(false),
        Action::make('ordered')->centerModal()->modalAlignment(Alignment::Start),
        TextInput::make('supported')->autocomplete(true)->autocapitalize('words'),
    ];
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\BehavioralConfiguration;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\AssociateAction;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload as Upload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;

function components(): array
{
    return [
        BaseFileUpload::make('file')->previewable(false),
        Upload::make('photo')->previewable(true)->imageAspectRatio('16:9')->automaticallyCropImagesToAspectRatio(true),
        Upload::make('blank')->imageAspectRatio('   ')->automaticallyCropImagesToAspectRatio(false),
        Upload::make('zero')->imageAspectRatio('0')->automaticallyCropImagesToAspectRatio(true),
        Upload::make('reset')->imageAspectRatio('4:3')->automaticallyCropImagesToAspectRatio()->imageAspectRatio(null)->automaticallyCropImagesToAspectRatio(false),
        (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))(Builder::make('blocks')->addable(false)->deletable(true)->reorderable(false))(),
        (static fn($component) => ($component instanceof Builder || $component instanceof Repeater) && !\method_exists($component, 'inset') ? static fn(bool|Closure $condition = true) => $component : $component->{'inset'}(...))(Repeater::make('items')->addable(true)->deletable(false)->reorderable(true))(condition: false),
        KeyValue::make('metadata')->addable(false)->deletable(true)->editableKeys(true)->editableValues(false),
        TextInput::make('name')->autocomplete(null)->autocapitalize(false),
        TextInput::make('other')->autocomplete(false)->autocapitalize(null),
        Textarea::make('description')->autocomplete(null),
        Select::make('status')->selectablePlaceholder(false),
        Select::make('other_status')->selectablePlaceholder(true),
        DateTimePicker::make('published')->date(false)->seconds(false)->time(true)->suffixIcon(Heroicon::Calendar, isInline: true),
        DatePicker::make('date')->suffixIcon(Heroicon::Calendar, isInline: true),
        TimePicker::make('time')->suffixIcon('heroicon-o-clock', isInline: true),
        DateTimePicker::make('unchanged')->suffixIcon('heroicon-o-star', isInline: false),
        Action::make('start')->modalAlignment(Alignment::Start),
        Action::make('end')->modalAlignment(Alignment::End),
        Action::make('center')->modalAlignment(Alignment::End)->modalAlignment(Alignment::Center),
        CreateAction::make()->createAnother(false),
        AttachAction::make()->attachAnother(true),
        AssociateAction::make()->associateAnother(false),
        Upload::make('ordered')->imageAspectRatio('16:9')->automaticallyCropImagesToAspectRatio(true)->automaticallyCropImagesToAspectRatio(false),
        Action::make('ordered')->modalAlignment(Alignment::Center)->modalAlignment(Alignment::Start),
        TextInput::make('supported')->autocomplete(true)->autocapitalize('words'),
    ];
}
