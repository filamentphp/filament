<?php

namespace App\Livewire\Forms;

use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class FieldsDemo extends Component implements HasForms
{
    use InteractsWithForms;

    public $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                Group::make()
                    ->id('simple')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('simple')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->id('helperText')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('helperText')
                            ->label('Name')
                            ->default('Dan Harrin')
                            ->helperText(str('Your **full name** here, including any middle names.')->inlineMarkdown()->toHtmlString()),
                    ]),
                Group::make()
                    ->id('hint')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('hint')
                            ->label('Password')
                            ->password()
                            ->default('password')
                            ->hint(str('[Forgotten your password?](forgotten-password)')->inlineMarkdown()->toHtmlString()),
                    ]),
                Group::make()
                    ->id('hintColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        RichEditor::make('hintColor')
                            ->label('Content')
                            ->default('Filament es el mejor.')
                            ->hint('Translatable')
                            ->hintColor('primary'),
                    ]),
                Group::make()
                    ->id('hintIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        RichEditor::make('hintIcon')
                            ->label('Content')
                            ->default('Filament es el mejor.')
                            ->hint('Translatable')
                            ->hintIcon('heroicon-m-language'),
                    ]),
                Group::make()
                    ->id('disabled')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('disabled')
                            ->label('Name')
                            ->disabled()
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->id('placeholder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('placeholder')
                            ->label('Name')
                            ->placeholder('Dan Harrin'),
                    ]),
                Group::make()
                    ->id('textInput')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('textInput')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->id('textInputAffix')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('textInputAffix')
                            ->label('Domain')
                            ->default('filamentphp')
                            ->prefix('https://')
                            ->suffix('.com'),
                    ]),
                Group::make()
                    ->id('textInputSuffixIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('textInputSuffixIcon')
                            ->label('Domain')
                            ->default('https://filamentphp.com')
                            ->suffixIcon('heroicon-m-globe-alt'),
                    ]),
                Group::make()
                    ->id('select')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('select')
                            ->label('Status'),
                    ]),
                Group::make()
                    ->id('javascriptSelect')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-48 max-w-xl',
                    ])
                    ->schema([
                        Select::make('javascriptSelect')
                            ->label('Status')
                            ->native(false)
                            ->options([
                                'draft' => 'Draft',
                                'reviewing' => 'Reviewing',
                                'published' => 'Published',
                            ]),
                    ]),
                Group::make()
                    ->id('searchableSelect')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-72 max-w-xl',
                    ])
                    ->schema([
                        Select::make('searchableSelect')
                            ->label('Author')
                            ->searchable()
                            ->options([
                                'dan' => 'Dan Harrin',
                                'ryan' => 'Ryan Chandler',
                                'zep' => 'Zep Fietje',
                                'dennis' => 'Dennis Koch',
                                'adam' => 'Adam Weston',
                            ]),
                    ]),
                Group::make()
                    ->id('multipleSelect')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-44 max-w-xl',
                    ])
                    ->schema([
                        Select::make('multipleSelect')
                            ->label('Technologies')
                            ->multiple()
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ]),
                    ]),
                Group::make()
                    ->id('groupedSelect')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->schema([
                        Select::make('groupedSelect')
                            ->label('Status')
                            ->searchable()
                            ->options([
                                'In Process' => [
                                    'draft' => 'Draft',
                                    'reviewing' => 'Reviewing',
                                ],
                                'Reviewed' => [
                                    'published' => 'Published',
                                    'rejected' => 'Rejected',
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('createSelectOption')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('createSelectOption')
                            ->label('Author')
                            ->createOptionForm([
                                TextInput::make('name'),
                            ]),
                    ]),
                Group::make()
                    ->id('editSelectOption')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('editSelectOption')
                            ->label('Author')
                            ->default('dan')
                            ->options([
                                'dan' => 'Dan Harrin',
                            ])
                            ->fillEditOptionActionFormUsing(fn () => ['name' => 'Dan Harrin'])
                            ->editOptionForm([
                                TextInput::make('name'),
                            ]),
                    ]),
                Group::make()
                    ->id('selectAffix')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('selectAffix')
                            ->label('Domain')
                            ->default('filament')
                            ->options([
                                'filament' => 'filamentphp',
                            ])
                            ->prefix('https://')
                            ->suffix('.com'),
                    ]),
                Group::make()
                    ->id('selectSuffixIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Select::make('selectSuffixIcon')
                            ->label('Domain')
                            ->default('filament')
                            ->options([
                                'filament' => 'filamentphp',
                            ])
                            ->suffixIcon('heroicon-m-globe-alt'),
                    ]),
                Group::make()
                    ->id('checkbox')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Checkbox::make('checkbox')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->id('inlineCheckbox')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Checkbox::make('inlineCheckbox')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->id('notInlineCheckbox')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Checkbox::make('notInlineCheckbox')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->id('toggle')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('toggle')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->id('toggleIcons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('toggleIcons')
                            ->label('Is admin')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user'),
                    ]),
                Group::make()
                    ->id('toggleOffColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('toggleOffColor')
                            ->label('Is admin')
                            ->default(false)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Group::make()
                    ->id('toggleOnColor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('toggleOnColor')
                            ->label('Is admin')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Group::make()
                    ->id('inlineToggle')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('inlineToggle')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->id('notInlineToggle')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Toggle::make('notInlineToggle')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->id('checkboxList')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('checkboxList')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ]),
                    ]),
                Group::make()
                    ->id('checkboxListOptionDescriptions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('checkboxListOptionDescriptions')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->descriptions([
                                'tailwind' => 'A utility-first CSS framework for rapidly building modern websites without ever leaving your HTML.',
                                'alpine' => new HtmlString('A rugged, minimal tool for composing behavior <strong>directly in your markup</strong>.'),
                                'laravel' => str('A **web application** framework with expressive, elegant syntax.')->inlineMarkdown()->toHtmlString(),
                                'livewire' => 'A full-stack framework for Laravel building dynamic interfaces simple, without leaving the comfort of Laravel.',
                            ]),
                    ]),
                Group::make()
                    ->id('checkboxListColumns')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('checkboxListColumns')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel'])
                            ->columns(2),
                    ]),
                Group::make()
                    ->id('checkboxListRows')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('checkboxListRows')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel'])
                            ->columns(2)
                            ->gridDirection('row'),
                    ]),
                Group::make()
                    ->id('searchableCheckboxList')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('searchableCheckboxList')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel'])
                            ->searchable(),
                    ]),
                Group::make()
                    ->id('bulkToggleableCheckboxList')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        CheckboxList::make('bulkToggleableCheckboxList')
                            ->label('Technologies')
                            ->options([
                                'tailwind' => 'Tailwind CSS',
                                'alpine' => 'Alpine.js',
                                'laravel' => 'Laravel',
                                'livewire' => 'Laravel Livewire',
                            ])
                            ->default(['tailwind', 'laravel'])
                            ->bulkToggleable(),
                    ]),
                Group::make()
                    ->id('radio')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('radio')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->id('radioOptionDescriptions')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('radioOptionDescriptions')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->descriptions([
                                'draft' => 'Is not visible.',
                                'scheduled' => 'Will be visible.',
                                'published' => 'Is visible.',
                            ])
                            ->default('draft'),
                    ]),
                Group::make()
                    ->id('booleanRadio')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('booleanRadio')
                            ->label('Do you like this post?')
                            ->boolean()
                            ->default(true),
                    ]),
                Group::make()
                    ->id('inlineRadio')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('inlineRadio')
                            ->label('Do you like this post?')
                            ->boolean()
                            ->inline()
                            ->default(true),
                    ]),
                Group::make()
                    ->id('disabledOptionRadio')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Radio::make('disabledOptionRadio')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'scheduled' => 'Scheduled',
                                'published' => 'Published',
                            ])
                            ->default('draft')
                            ->disableOptionWhen(fn (string $value): bool => $value === 'published'),
                    ]),
                Group::make()
                    ->id('dateTimePickers')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        DateTimePicker::make('dateTimePicker')
                            ->label('Published at'),
                        DatePicker::make('datePickers')
                            ->label('Date of birth'),
                        TimePicker::make('timePicker')
                            ->label('Alarm at'),
                    ]),
                Group::make()
                    ->id('dateTimePickerWithoutSeconds')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        DateTimePicker::make('dateTimePickerWithoutSeconds')
                            ->label('Published at')
                            ->seconds(false),
                    ]),
                Group::make()
                    ->id('javascriptDateTimePicker')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('javascriptDateTimePicker')
                            ->label('Date of birth')
                            ->native(false)
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerDisplayFormat')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerDisplayFormat')
                            ->label('Date of birth')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerWeekStartsOnSunday')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerWeekStartsOnSunday')
                            ->label('Published at')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerDisabledDates')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerDisabledDates')
                            ->label('Date')
                            ->native(false)
                            ->disabledDates(['2000-01-03', '2000-01-15', '2000-01-20'])
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerAffix')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerAffix')
                            ->label('Date')
                            ->prefix('Starts')
                            ->suffix('at midnight')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('dateTimePickerPrefixIcon')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TimePicker::make('dateTimePickerPrefixIcon')
                            ->label('At')
                            ->prefixIcon('heroicon-m-play')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->id('fileUpload')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        FileUpload::make('fileUpload')
                            ->label('Attachment'),
                    ]),
                Group::make()
                    ->id('richEditor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        RichEditor::make('richEditor')
                            ->label('Content'),
                    ]),
                Group::make()
                    ->id('markdownEditor')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        MarkdownEditor::make('markdownEditor')
                            ->label('Content'),
                    ]),
                Group::make()
                    ->id('repeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('repeater')
                            ->label('Members')
                            ->schema([
                                TextInput::make('name')->required(),
                                Select::make('role')
                                    ->options([
                                        'member' => 'Member',
                                        'administrator' => 'Administrator',
                                        'owner' => 'Owner',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->default([
                                [
                                    'name' => 'Dan Harrin',
                                    'role' => 'owner',
                                ],
                                [
                                    'name' => 'Ryan Chandler',
                                    'role' => 'administrator',
                                ],
                                [
                                    'name' => 'Zep Fietje',
                                    'role' => 'member',
                                ],
                                [
                                    'name' => null,
                                    'role' => null,
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('repeaterReorderableWithButtons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('repeaterReorderableWithButtons')
                            ->label('Members')
                            ->schema([
                                TextInput::make('name')->required(),
                                Select::make('role')
                                    ->options([
                                        'member' => 'Member',
                                        'administrator' => 'Administrator',
                                        'owner' => 'Owner',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->default([
                                [
                                    'name' => 'Dan Harrin',
                                    'role' => 'owner',
                                ],
                                [
                                    'name' => 'Ryan Chandler',
                                    'role' => 'administrator',
                                ],
                                [
                                    'name' => 'Zep Fietje',
                                    'role' => 'member',
                                ],
                            ])
                            ->reorderableWithButtons(),
                    ]),
                Group::make()
                    ->id('collapsedRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('collapsedRepeater')
                            ->label('Qualifications')
                            ->defaultItems(3)
                            ->collapsed(),
                    ]),
                Group::make()
                    ->id('cloneableRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('cloneableRepeater')
                            ->label('Qualifications')
                            ->schema([
                                TextInput::make('name')->required(),
                            ])
                            ->cloneable()
                            ->default([
                                ['name' => 'Tailwind CSS Level 1'],
                                ['name' => 'Alpine.js Level 1'],
                                ['name' => 'Laravel Level 1'],
                                ['name' => 'Livewire Level 1'],
                            ]),
                    ]),
                Group::make()
                    ->id('gridRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('gridRepeater')
                            ->label('Qualifications')
                            ->schema([
                                TextInput::make('name')->required(),
                            ])
                            ->grid(2)
                            ->default([
                                ['name' => 'Tailwind CSS Level 1'],
                                ['name' => 'Alpine.js Level 1'],
                                ['name' => 'Laravel Level 1'],
                                ['name' => 'Livewire Level 1'],
                            ]),
                    ]),
                Group::make()
                    ->id('labelledRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('labelledRepeater')
                            ->label('Members')
                            ->schema([
                                TextInput::make('name')->required(),
                                Select::make('role')
                                    ->options([
                                        'member' => 'Member',
                                        'administrator' => 'Administrator',
                                        'owner' => 'Owner',
                                    ])
                                    ->required(),
                            ])
                            ->columns(2)
                            ->default([
                                [
                                    'name' => 'Dan Harrin',
                                    'role' => 'owner',
                                ],
                                [
                                    'name' => 'Ryan Chandler',
                                    'role' => 'administrator',
                                ],
                                [
                                    'name' => 'Zep Fietje',
                                    'role' => 'member',
                                ],
                            ])
                            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null),
                    ]),
                Group::make()
                    ->id('simpleRepeater')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Repeater::make('simpleRepeater')
                            ->label('Invitations')
                            ->simple(
                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                            )
                            ->default([
                                'dan@filamentphp.com',
                                'ryan@filamentphp.com',
                            ]),
                    ]),
                Group::make()
                    ->id('builder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('builder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('heading')
                                    ->schema([
                                        TextInput::make('content')
                                            ->label('Heading')
                                            ->required(),
                                        Select::make('level')
                                            ->options([
                                                'h1' => 'Heading 1',
                                                'h2' => 'Heading 2',
                                                'h3' => 'Heading 3',
                                                'h4' => 'Heading 4',
                                                'h5' => 'Heading 5',
                                                'h6' => 'Heading 6',
                                            ])
                                            ->required(),
                                    ])
                                    ->columns(2),
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ]),
                                Builder\Block::make('image')
                                    ->schema([
                                        FileUpload::make('url')
                                            ->label('Image')
                                            ->image()
                                            ->required(),
                                        TextInput::make('alt')
                                            ->label('Alt text')
                                            ->required(),
                                    ]),
                            ])
                            ->default([
                                [
                                    'type' => 'heading',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet',
                                        'level' => 'h2',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'image',
                                    'data' => [
                                        'url' => null,
                                        'alt' => 'Lorem ipsum dolor sit amet',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'image',
                                    'data' => [
                                        'url' => null,
                                        'alt' => 'Lorem ipsum dolor sit amet',
                                    ],
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('labelledBuilder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('labelledBuilder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('heading')
                                    ->schema([
                                        TextInput::make('content')
                                            ->label('Heading')
                                            ->required(),
                                        Select::make('level')
                                            ->options([
                                                'h1' => 'Heading 1',
                                                'h2' => 'Heading 2',
                                                'h3' => 'Heading 3',
                                                'h4' => 'Heading 4',
                                                'h5' => 'Heading 5',
                                                'h6' => 'Heading 6',
                                            ])
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->label(function (?array $state): string {
                                        if ($state === null) {
                                            return 'Heading';
                                        }

                                        return $state['content'] ?? 'Untitled heading';
                                    }),
                            ])
                            ->default([
                                [
                                    'type' => 'heading',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet',
                                        'level' => 'h2',
                                    ],
                                ],
                                [
                                    'type' => 'heading',
                                    'data' => [
                                        'content' => null,
                                        'level' => 'h3',
                                    ],
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('builderIcons')
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-40 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('builderIcons')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ])
                                    ->icon('heroicon-m-bars-3-bottom-left'),
                                Builder\Block::make('image')
                                    ->icon('heroicon-m-photo'),
                            ])
                            ->default([
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                            ]),
                    ]),
                Group::make()
                    ->id('builderReorderableWithButtons')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('builderReorderableWithButtons')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ])
                                    ->icon('heroicon-m-bars-3-bottom-left'),
                            ])
                            ->default([
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                            ])
                            ->reorderableWithButtons(),
                    ]),
                Group::make()
                    ->id('collapsedBuilder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('collapsedBuilder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ])
                                    ->icon('heroicon-m-bars-3-bottom-left'),
                            ])
                            ->default([
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                            ])
                            ->collapsed(),
                    ]),
                Group::make()
                    ->id('cloneableBuilder')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        Builder::make('cloneableBuilder')
                            ->label('Content')
                            ->schema([
                                Builder\Block::make('paragraph')
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Paragraph')
                                            ->required(),
                                    ])
                                    ->icon('heroicon-m-bars-3-bottom-left'),
                            ])
                            ->default([
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                                [
                                    'type' => 'paragraph',
                                    'data' => [
                                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc. Donec euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.',
                                    ],
                                ],
                            ])
                            ->cloneable(),
                    ]),
                Group::make()
                    ->id('tagsInput')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TagsInput::make('tagsInput')
                            ->label('Tags')
                            ->default(['Tailwind CSS', 'Alpine.js']),
                    ]),
                Group::make()
                    ->id('textarea')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        Textarea::make('textarea')
                            ->label('Description')
                            ->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.'),
                    ]),
                Group::make()
                    ->id('keyValue')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        KeyValue::make('keyValue')
                            ->label('Meta')
                            ->default([
                                'description' => 'Filament is a collection of Laravel packages',
                                'og:type' => 'website',
                                'og:site_name' => 'Filament',
                            ]),
                    ]),
                Group::make()
                    ->id('reorderableKeyValue')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->schema([
                        KeyValue::make('reorderableKeyValue')
                            ->label('Meta')
                            ->default([
                                'description' => 'Filament is a collection of Laravel packages',
                                'og:type' => 'website',
                                'og:site_name' => 'Filament',
                            ])
                            ->reorderable(),
                    ]),
                Group::make()
                    ->id('colorPicker')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        ColorPicker::make('colorPicker')
                            ->label('Color')
                            ->default('#3490dc'),
                    ]),
                Group::make()
                    ->id('suffixAction')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('suffixAction')
                            ->label('Cost')
                            ->prefix('€')
                            ->default('22.66')
                            ->suffixAction(
                                Action::make('copyCostToPrice')
                                    ->icon('heroicon-m-clipboard'),
                            ),
                    ]),
                Group::make()
                    ->id('hintAction')
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->schema([
                        TextInput::make('hintAction')
                            ->label('Cost')
                            ->prefix('€')
                            ->default('22.66')
                            ->hintAction(
                                Action::make('copyCostToPrice')
                                    ->icon('heroicon-m-clipboard'),
                            ),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.forms.fields');
    }
}
