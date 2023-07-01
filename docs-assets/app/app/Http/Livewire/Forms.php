<?php

namespace App\Http\Livewire;

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
use Livewire\Component;

class Forms extends Component implements HasForms
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'simple',
                    ])
                    ->schema([
                        TextInput::make('simple')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'helperText',
                    ])
                    ->schema([
                        TextInput::make('helperText')
                            ->label('Name')
                            ->default('Dan Harrin')
                            ->helperText('Your **full name** here, including any middle names.'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'hint',
                    ])
                    ->schema([
                        TextInput::make('hint')
                            ->label('Password')
                            ->password()
                            ->default('password')
                            ->hint('[Forgotten your password?](forgotten-password)'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'hintColor',
                    ])
                    ->schema([
                        RichEditor::make('hintColor')
                            ->label('Content')
                            ->default('Filament es el mejor.')
                            ->hint('Translatable')
                            ->hintColor('primary'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'hintIcon',
                    ])
                    ->schema([
                        RichEditor::make('hintIcon')
                            ->label('Content')
                            ->default('Filament es el mejor.')
                            ->hint('Translatable')
                            ->hintIcon('heroicon-m-language'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'disabled',
                    ])
                    ->schema([
                        TextInput::make('disabled')
                            ->label('Name')
                            ->disabled()
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'placeholder',
                    ])
                    ->schema([
                        TextInput::make('placeholder')
                            ->label('Name')
                            ->placeholder('John Doe'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'textInput',
                    ])
                    ->schema([
                        TextInput::make('textInput')
                            ->label('Name')
                            ->default('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'textInputAffix',
                    ])
                    ->schema([
                        TextInput::make('textInputAffix')
                            ->label('Domain')
                            ->default('filamentphp')
                            ->prefix('https://')
                            ->suffix('.com'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'textInputSuffixIcon',
                    ])
                    ->schema([
                        TextInput::make('textInputSuffixIcon')
                            ->label('Domain')
                            ->default('https://filamentphp.com')
                            ->suffixIcon('heroicon-m-globe-alt'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'select',
                    ])
                    ->schema([
                        Select::make('select')
                            ->label('Status'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-72 max-w-xl',
                        'id' => 'searchableSelect',
                    ])
                    ->schema([
                        Select::make('searchableSelect')
                            ->label('Author')
                            ->searchable()
                            ->options([
                                'dan' => 'Dan Harrin',
                                'ryan' => 'Ryan Chandler',
                                'zep' => 'Zep Fietje',
                                'adam' => 'Adam Weston',
                                'dennis' => 'Dennis Koch',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-44 max-w-xl',
                        'id' => 'multipleSelect',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'createSelectOption',
                    ])
                    ->schema([
                        Select::make('createSelectOption')
                            ->label('Author')
                            ->createOptionForm([
                                TextInput::make('name'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'editSelectOption',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'selectAffix',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'selectSuffixIcon',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'checkbox',
                    ])
                    ->schema([
                        Checkbox::make('checkbox')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'inlineCheckbox',
                    ])
                    ->schema([
                        Checkbox::make('inlineCheckbox')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'notInlineCheckbox',
                    ])
                    ->schema([
                        Checkbox::make('notInlineCheckbox')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'toggle',
                    ])
                    ->schema([
                        Toggle::make('toggle')
                            ->label('Is admin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'toggleIcons',
                    ])
                    ->schema([
                        Toggle::make('toggleIcons')
                            ->label('Is admin')
                            ->onIcon('heroicon-m-bolt')
                            ->offIcon('heroicon-m-user'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'toggleOffColor',
                    ])
                    ->schema([
                        Toggle::make('toggleOffColor')
                            ->label('Is admin')
                            ->default(false)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'toggleOnColor',
                    ])
                    ->schema([
                        Toggle::make('toggleOnColor')
                            ->label('Is admin')
                            ->default(true)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'inlineToggle',
                    ])
                    ->schema([
                        Toggle::make('inlineToggle')
                            ->label('Is admin')
                            ->inline(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'notInlineToggle',
                    ])
                    ->schema([
                        Toggle::make('notInlineToggle')
                            ->label('Is admin')
                            ->inline(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'checkboxList',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'checkboxListColumns',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'checkboxListRows',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'searchableCheckboxList',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'bulkToggleableCheckboxList',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'radio',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'radioOptionDescriptions',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'booleanRadio',
                    ])
                    ->schema([
                        Radio::make('booleanRadio')
                            ->label('Do you like this post?')
                            ->boolean()
                            ->default(true),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'inlineRadio',
                    ])
                    ->schema([
                        Radio::make('inlineRadio')
                            ->label('Do you like this post?')
                            ->boolean()
                            ->inline()
                            ->default(true),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'disabledOptionRadio',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'dateTimePickers',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'dateTimePickerWithoutSeconds',
                    ])
                    ->schema([
                        DateTimePicker::make('dateTimePickerWithoutSeconds')
                            ->label('Published at')
                            ->seconds(false),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                        'id' => 'javascriptDateTimePicker',
                    ])
                    ->schema([
                        DatePicker::make('javascriptDateTimePicker')
                            ->label('Date of birth')
                            ->native(false)
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'dateTimePickerDisplayFormat',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerDisplayFormat')
                            ->label('Date of birth')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                        'id' => 'dateTimePickerWeekStartsOnSunday',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerWeekStartsOnSunday')
                            ->label('Published at')
                            ->native(false)
                            ->weekStartsOnSunday()
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-96 max-w-xl',
                        'id' => 'dateTimePickerDisabledDates',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerDisabledDates')
                            ->label('Date')
                            ->native(false)
                            ->disabledDates(['2000-01-03', '2000-01-15', '2000-01-20'])
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'dateTimePickerAffix',
                    ])
                    ->schema([
                        DatePicker::make('dateTimePickerAffix')
                            ->label('Date')
                            ->prefix('Starts')
                            ->suffix('at midnight')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'dateTimePickerPrefixIcon',
                    ])
                    ->schema([
                        TimePicker::make('dateTimePickerPrefixIcon')
                            ->label('At')
                            ->prefixIcon('heroicon-m-play')
                            ->default('2000-01-01'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'fileUpload',
                    ])
                    ->schema([
                        FileUpload::make('fileUpload')
                            ->label('Attachment'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'richEditor',
                    ])
                    ->schema([
                        RichEditor::make('richEditor')
                            ->label('Content'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'markdownEditor',
                    ])
                    ->schema([
                        MarkdownEditor::make('markdownEditor')
                            ->label('Content'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'repeater',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'repeaterReorderableWithButtons',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'collapsedRepeater',
                    ])
                    ->schema([
                        Repeater::make('collapsedRepeater')
                            ->label('Qualifications')
                            ->defaultItems(3)
                            ->collapsed(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'cloneableRepeater',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'gridRepeater',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'labelledRepeater',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'insetRepeater',
                    ])
                    ->schema([
                        Repeater::make('insetRepeater')
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
                            ->inset(),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'builder',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'labelledBuilder',
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
                    ->extraAttributes([
                        'class' => 'px-16 pt-16 pb-40 max-w-5xl',
                        'id' => 'builderIcons',
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
                        Group::make()
                            ->extraAttributes([
                                'class' => 'p-16 max-w-5xl',
                                'id' => 'builderReorderableWithButtons',
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
                            ->extraAttributes([
                                'class' => 'p-16 max-w-5xl',
                                'id' => 'collapsedBuilder',
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
                            ->extraAttributes([
                                'class' => 'p-16 max-w-5xl',
                                'id' => 'cloneableBuilder',
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
                            ->extraAttributes([
                                'class' => 'p-16 max-w-5xl',
                                'id' => 'insetBuilder',
                            ])
                            ->schema([
                                Builder::make('insetBuilder')
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
                                    ->inset(),
                                ]),
                        ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'tagsInput',
                    ])
                    ->schema([
                        TagsInput::make('tagsInput')
                            ->label('Tags')
                            ->default(['Tailwind CSS', 'Alpine.js']),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'textarea',
                    ])
                    ->schema([
                        Textarea::make('textarea')
                            ->label('Description')
                            ->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, quam sapien aliquet nunc, eget aliquam velit nisl quis nunc.'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'keyValue',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                        'id' => 'reorderableKeyValue',
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
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                        'id' => 'colorPicker',
                    ])
                    ->schema([
                        ColorPicker::make('colorPicker')
                            ->label('Color')
                            ->default('#3490dc'),
                    ]),
            ]);
    }

    public function render()
    {
        return view('livewire.forms');
    }
}
