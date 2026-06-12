<?php

namespace App\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\SlideOverPosition;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

class ActionsDemo extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public function buttonAction(): Action
    {
        return Action::make('button')
            ->label('Edit')
            ->button();
    }

    public function linkAction(): Action
    {
        return Action::make('link')
            ->label('Edit')
            ->link();
    }

    public function iconButtonAction(): Action
    {
        return Action::make('iconButton')
            ->icon(Heroicon::PencilSquare)
            ->iconButton();
    }

    public function badgeAction(): Action
    {
        return Action::make('badge')
            ->label('Edit')
            ->badge();
    }

    public function dangerAction(): Action
    {
        return Action::make('danger')
            ->label('Delete')
            ->color('danger');
    }

    public function largeAction(): Action
    {
        return Action::make('large')
            ->label('Create')
            ->size(Size::Large);
    }

    public function iconAction(): Action
    {
        return Action::make('icon')
            ->label('Edit')
            ->icon(Heroicon::PencilSquare);
    }

    public function iconAfterAction(): Action
    {
        return Action::make('iconAfter')
            ->label('Edit')
            ->icon(Heroicon::PencilSquare)
            ->iconPosition(IconPosition::After);
    }

    public function badgedAction(): Action
    {
        return Action::make('badged')
            ->iconButton()
            ->icon(Heroicon::Funnel)
            ->badge(5);
    }

    public function successBadgedAction(): Action
    {
        return Action::make('successBadged')
            ->iconButton()
            ->icon(Heroicon::Funnel)
            ->badge(5)
            ->badgeColor('success');
    }

    public function disabledAction(): Action
    {
        return Action::make('disabled')
            ->label('Delete')
            ->color('danger')
            ->disabled();
    }

    public function outlinedAction(): Action
    {
        return Action::make('outlined')
            ->label('Edit')
            ->button()
            ->outlined();
    }

    public function confirmationModalAction(): Action
    {
        return Action::make('confirmationModal')
            ->label('Delete')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn () => null);
    }

    public function confirmationModalCustomTextAction(): Action
    {
        return Action::make('confirmationModalCustomText')
            ->label('Delete')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Delete post')
            ->modalDescription('Are you sure you\'d like to delete this post? This cannot be undone.')
            ->modalSubmitActionLabel('Yes, delete it')
            ->action(fn () => null);
    }

    public function modalIconAction(): Action
    {
        return Action::make('modalIcon')
            ->label('Delete')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn () => null)
            ->modalIcon(Heroicon::OutlinedTrash);
    }

    public function modalFormAction(): Action
    {
        return Action::make('modalForm')
            ->label('Update author')
            ->schema([
                Select::make('authorId')
                    ->label('Author')
                    ->required(),
            ])
            ->action(fn () => null);
    }

    public function wizardAction(): Action
    {
        return Action::make('wizard')
            ->label('Create')
            ->steps([
                Step::make('Name')
                    ->description('Give the category unique name')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('slug')
                            ->disabled()
                            ->required(),
                    ])
                    ->columns(2),
                Step::make('Description')
                    ->description('Add some extra details')
                    ->schema([]),
                Step::make('Visibility')
                    ->description('Control who can view it')
                    ->schema([]),
            ])
            ->action(fn () => null);
    }

    public function modalSchemaAction(): Action
    {
        return Action::make('modalSchema')
            ->label('View user')
            ->schema([
                Grid::make(2)
                    ->schema([
                        Section::make('Details')
                            ->schema([
                                TextInput::make('name')
                                    ->default('Dan Harrin'),
                                Select::make('position')
                                    ->options([
                                        'developer' => 'Developer',
                                        'designer' => 'Designer',
                                        'manager' => 'Manager',
                                    ])
                                    ->default('developer'),
                                Checkbox::make('is_admin')
                                    ->label('Administrator')
                                    ->default(true),
                            ]),
                        Section::make('Auditing')
                            ->schema([
                                TextEntry::make('created_at')
                                    ->state('Jan 15, 2025 09:30:00'),
                                TextEntry::make('updated_at')
                                    ->state('Mar 12, 2025 14:22:00'),
                            ]),
                    ]),
            ])
            ->action(fn () => null);
    }

    public function wideModalAction(): Action
    {
        return Action::make('wideModal')
            ->label('Update author')
            ->schema([
                Grid::make(2)
                    ->schema([
                        TextInput::make('name')
                            ->default('Dan Harrin'),
                        TextInput::make('email')
                            ->default('dan@filamentphp.com'),
                    ]),
            ])
            ->modalWidth(Width::FiveExtraLarge)
            ->action(fn () => null);
    }

    public function modalAlignmentAction(): Action
    {
        return Action::make('modalAlignment')
            ->label('Subscribe')
            ->requiresConfirmation()
            ->modalHeading('Subscribe to newsletter')
            ->modalDescription('You will receive weekly updates about new features and improvements.')
            ->modalAlignment(Alignment::Center)
            ->action(fn () => null);
    }

    public function stickyModalHeaderAction(): Action
    {
        return Action::make('stickyModalHeader')
            ->label('Edit profile')
            ->schema([
                TextInput::make('name')
                    ->default('Dan Harrin'),
                TextInput::make('email')
                    ->default('dan@filamentphp.com'),
                Select::make('timezone')
                    ->options([
                        'UTC' => 'UTC',
                        'America/New_York' => 'Eastern Time',
                        'America/Chicago' => 'Central Time',
                        'America/Denver' => 'Mountain Time',
                        'America/Los_Angeles' => 'Pacific Time',
                    ])
                    ->default('UTC'),
                TextInput::make('phone')
                    ->label('Phone number')
                    ->default('+1 (555) 123-4567'),
                Select::make('language')
                    ->options([
                        'en' => 'English',
                        'es' => 'Spanish',
                        'fr' => 'French',
                        'de' => 'German',
                    ])
                    ->default('en'),
                TextInput::make('company')
                    ->default('Filament'),
                TextInput::make('job_title')
                    ->label('Job title')
                    ->default('Software Developer'),
            ])
            ->stickyModalHeader()
            ->stickyModalFooter()
            ->action(fn () => null);
    }

    public function slideOverAction(): Action
    {
        return Action::make('slideOver')
            ->label('Update author')
            ->schema([
                Select::make('authorId')
                    ->label('Author')
                    ->required(),
            ])
            ->slideOver()
            ->action(fn () => null);
    }

    public function slideOverStartAction(): Action
    {
        return Action::make('slideOverStart')
            ->label('Update author')
            ->schema([
                Select::make('authorId')
                    ->label('Author')
                    ->required(),
            ])
            ->slideOver()
            ->slideOverPosition(SlideOverPosition::Start)
            ->action(fn () => null);
    }

    public function modalNoCloseButtonAction(): Action
    {
        return Action::make('modalNoCloseButton')
            ->label('Delete post')
            ->requiresConfirmation()
            ->modalHeading('Delete post')
            ->modalDescription('Are you sure you want to delete this post? This action cannot be undone.')
            ->modalIcon(Heroicon::OutlinedTrash)
            ->modalIconColor('danger')
            ->modalCloseButton(false)
            ->action(fn () => null);
    }

    public function disabledFormAction(): Action
    {
        return Action::make('disabledForm')
            ->label('Approve post')
            ->schema([
                TextInput::make('title')
                    ->default('10 Tips for Better Laravel Performance'),
                Textarea::make('content')
                    ->default('In this article, we explore proven strategies for optimizing your Laravel applications, from query optimization to caching techniques.')
                    ->rows(3),
            ])
            ->fillForm([
                'title' => '10 Tips for Better Laravel Performance',
                'content' => 'In this article, we explore proven strategies for optimizing your Laravel applications, from query optimization to caching techniques.',
            ])
            ->disabledForm()
            ->modalSubmitActionLabel('Approve')
            ->action(fn () => null);
    }

    public function modalIconColorAction(): Action
    {
        return Action::make('modalIconColor')
            ->label('Delete')
            ->color('danger')
            ->requiresConfirmation()
            ->modalIcon(Heroicon::OutlinedTrash)
            ->modalIconColor('warning')
            ->action(fn () => null);
    }

    public function extraFooterActionsAction(): Action
    {
        return Action::make('extraFooterActions')
            ->label('Create post')
            ->schema([
                TextInput::make('title')
                    ->required(),
                Textarea::make('content'),
            ])
            ->extraModalFooterActions(fn (Action $action): array => [
                $action->makeModalSubmitAction('createAnother', arguments: ['another' => true])
                    ->label('Create & create another'),
            ])
            ->action(fn () => null);
    }

    public function overlayingChildModalAction(): Action
    {
        return Action::make('overlayingChildModal')
            ->label('Edit items')
            ->slideOver()
            ->schema([
                Repeater::make('items')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('quantity')
                            ->numeric()
                            ->required(),
                    ])
                    ->columns(2)
                    ->deleteAction(
                        fn (Action $action) => $action
                            ->requiresConfirmation()
                            ->overlayParentActions(),
                    )
                    ->default([
                        [
                            'name' => 'Widget A',
                            'quantity' => '10',
                        ],
                        [
                            'name' => 'Widget B',
                            'quantity' => '25',
                        ],
                        [
                            'name' => 'Widget C',
                            'quantity' => '5',
                        ],
                    ]),
            ])
            ->action(fn () => null);
    }

    public function authorizationTooltipAction(): Action
    {
        return Action::make('authorizationTooltip')
            ->label('Edit')
            ->icon(Heroicon::PencilSquare)
            ->disabled()
            ->tooltip('You do not have permission to edit this record.');
    }

    public function render()
    {
        return view('livewire.actions');
    }
}
