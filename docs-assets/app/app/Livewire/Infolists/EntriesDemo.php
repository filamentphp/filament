<?php

namespace App\Livewire\Infolists;

use Filament\Actions\Action;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;
use Phiki\Grammar\Grammar;
use Phiki\Theme\Theme;

class EntriesDemo extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('simple')
                    ->schema([
                        TextEntry::make('title')
                            ->state('What is Filament?'),
                        TextEntry::make('author.name')
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('placeholder')
                    ->schema([
                        TextEntry::make('title')
                            ->placeholder('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('inlineLabel')
                    ->schema([
                        TextEntry::make('name')
                            ->inlineLabel()
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('inlineLabelSection')
                    ->schema([
                        Section::make('Details')
                            ->inlineLabel()
                            ->schema([
                                TextEntry::make('name')
                                    ->state('Dan Harrin'),
                                TextEntry::make('emailAddress')
                                    ->state('dan@filamentphp.com'),
                                TextEntry::make('phoneNumber')
                                    ->state('123-456-7890'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('tooltips')
                    ->schema([
                        TextEntry::make('title')
                            ->tooltip('Shown at the top of the page')
                            ->state('What is Filament?'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textBelowContent')
                    ->schema([
                        TextEntry::make('name')
                            ->belowContent('This is the user\'s full name.')
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('componentBelowContent')
                    ->schema([
                        TextEntry::make('name')
                            ->belowContent(Text::make('This is the user\'s full name.')->weight(FontWeight::Bold))
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('actionBelowContent')
                    ->schema([
                        TextEntry::make('name')
                            ->belowContent(Action::make('generate'))
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('belowContent')
                    ->schema([
                        TextEntry::make('name')
                            ->belowContent([
                                Icon::make(Heroicon::InformationCircle),
                                'This is the user\'s full name.',
                                Action::make('generate'),
                            ])
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('belowContentAlignment')
                    ->schema([
                        TextEntry::make('name')
                            ->belowContent(Schema::end([
                                Icon::make(Heroicon::InformationCircle),
                                'This is the user\'s full name.',
                                Action::make('generate'),
                            ]))
                            ->state('Dan Harrin'),
                        TextEntry::make('name')
                            ->belowContent(Schema::between([
                                Icon::make(Heroicon::InformationCircle),
                                'This is the user\'s full name.',
                                Action::make('generate'),
                            ]))
                            ->state('Dan Harrin'),
                        TextEntry::make('name')
                            ->belowContent(Schema::between([
                                Flex::make([
                                    Icon::make(Heroicon::InformationCircle)
                                        ->grow(false),
                                    'This is the user\'s full name.',
                                ]),
                                Action::make('generate'),
                            ]))
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('aboveLabel')
                    ->schema([
                        TextEntry::make('name')
                            ->aboveLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content above the entry\'s label',
                            ])
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('beforeLabel')
                    ->schema([
                        TextEntry::make('name')
                            ->beforeLabel(Icon::make(Heroicon::Star))
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('afterLabel')
                    ->schema([
                        TextEntry::make('name')
                            ->afterLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content after the entry\'s label',
                            ])
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('afterLabelAlignedStart')
                    ->schema([
                        TextEntry::make('name')
                            ->afterLabel(Schema::start([
                                Icon::make(Heroicon::Star),
                                'This is the content after the entry\'s label',
                            ]))
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('belowLabel')
                    ->schema([
                        TextEntry::make('name')
                            ->belowLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content below the entry\'s label',
                            ])
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('aboveContent')
                    ->schema([
                        TextEntry::make('name')
                            ->belowLabel([
                                Icon::make(Heroicon::Star),
                                'This is the content above the entry\'s content',
                            ])
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('beforeContent')
                    ->schema([
                        TextEntry::make('name')
                            ->beforeContent(Icon::make(Heroicon::Star))
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('afterContent')
                    ->schema([
                        TextEntry::make('name')
                            ->afterContent(Icon::make(Heroicon::Star))
                            ->state('Dan Harrin'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('text')
                    ->schema([
                        TextEntry::make('title')
                            ->state('What is Filament?'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textBadge')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'reviewing' => 'warning',
                                'published' => 'success',
                                'rejected' => 'danger',
                            })
                            ->state('published'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textList')
                    ->schema([
                        TextEntry::make('authors.name')
                            ->listWithLineBreaks()
                            ->state(['Dan Harrin', 'Ryan Chandler', 'Zep Fietje', 'Dennis Koch', 'Adam Weston']),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textBulletList')
                    ->schema([
                        TextEntry::make('authors.name')
                            ->bulleted()
                            ->listWithLineBreaks()
                            ->state(['Dan Harrin', 'Ryan Chandler', 'Zep Fietje', 'Dennis Koch', 'Adam Weston']),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textColor')
                    ->schema([
                        TextEntry::make('status')
                            ->color('primary')
                            ->state('Published'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textIcon')
                    ->schema([
                        TextEntry::make('email')
                            ->icon(Heroicon::Envelope)
                            ->state('dan@filamentphp.com'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textIconAfter')
                    ->schema([
                        TextEntry::make('email')
                            ->icon(Heroicon::Envelope)
                            ->iconPosition(IconPosition::After)
                            ->state('dan@filamentphp.com'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textIconColor')
                    ->schema([
                        TextEntry::make('email')
                            ->icon(Heroicon::Envelope)
                            ->iconColor('primary')
                            ->state('dan@filamentphp.com'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textLarge')
                    ->schema([
                        TextEntry::make('title')
                            ->size(TextSize::Large)
                            ->state('What is Filament?'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textBold')
                    ->schema([
                        TextEntry::make('title')
                            ->weight(FontWeight::Bold)
                            ->state('What is Filament?'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textMono')
                    ->schema([
                        TextEntry::make('apiKey')
                            ->label('API key')
                            ->fontFamily(FontFamily::Mono)
                            ->state('HGA3CH5AB345JD9MQ3'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('textCopyable')
                    ->schema([
                        TextEntry::make('apiKey')
                            ->label('API key')
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500)
                            ->copyable()
                            ->state('HGA3CH5AB345JD9MQ3'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('icon')
                    ->schema([
                        IconEntry::make('status')
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'draft' => Heroicon::OutlinedPencil,
                                'reviewing' => Heroicon::OutlinedClock,
                                'published' => Heroicon::OutlinedCheckCircle,
                            })
                            ->state('reviewing'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('iconColor')
                    ->schema([
                        IconEntry::make('status')
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'info',
                                'reviewing' => 'warning',
                                'published' => 'success',
                                default => 'gray',
                            })
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'draft' => Heroicon::OutlinedPencil,
                                'reviewing' => Heroicon::OutlinedClock,
                                'published' => Heroicon::OutlinedCheckCircle,
                            })
                            ->state('reviewing'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('iconMedium')
                    ->schema([
                        IconEntry::make('status')
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'danger',
                                'reviewing' => 'warning',
                                'published' => 'success',
                                default => 'gray',
                            })
                            ->icon(fn (string $state): Heroicon => match ($state) {
                                'draft' => Heroicon::OutlinedPencil,
                                'reviewing' => Heroicon::OutlinedClock,
                                'published' => Heroicon::OutlinedCheckCircle,
                            })
                            ->size(IconSize::Medium)
                            ->state('reviewing'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('iconBoolean')
                    ->schema([
                        IconEntry::make('is_featured')
                            ->boolean()
                            ->state(0),
                        IconEntry::make('is_featured')
                            ->boolean()
                            ->state(1),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('iconBooleanIcon')
                    ->schema([
                        IconEntry::make('is_featured')
                            ->boolean()
                            ->falseIcon(Heroicon::OutlinedXMark)
                            ->trueIcon(Heroicon::OutlinedCheckBadge)
                            ->state(0),
                        IconEntry::make('is_featured')
                            ->boolean()
                            ->falseIcon(Heroicon::OutlinedXMark)
                            ->trueIcon(Heroicon::OutlinedCheckBadge)
                            ->state(1),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('iconBooleanColor')
                    ->schema([
                        IconEntry::make('is_featured')
                            ->boolean()
                            ->falseColor('warning')
                            ->trueColor('info')
                            ->state(0),
                        IconEntry::make('is_featured')
                            ->boolean()
                            ->falseColor('warning')
                            ->trueColor('info')
                            ->state(1),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->id('image')
                    ->schema([
                        ImageEntry::make('header_image')
                            ->state('https://picsum.photos/id/12/1200/800'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->id('imageSquare')
                    ->schema([
                        ImageEntry::make('author.avatar')
                            ->height(40)
                            ->square()
                            ->state('https://picsum.photos/id/177/1200/800'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->id('imageCircular')
                    ->schema([
                        ImageEntry::make('author.avatar')
                            ->circular()
                            ->height(40)
                            ->state('https://picsum.photos/id/433/1200/800'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->id('imageStacked')
                    ->schema([
                        ImageEntry::make('colleagues')
                            ->circular()
                            ->height(40)
                            ->stacked()
                            ->state([
                                'https://avatars.githubusercontent.com/u/41837763?v=4',
                                'https://avatars.githubusercontent.com/u/44533235?v=4',
                                'https://avatars.githubusercontent.com/u/22632550?v=4',
                                'https://avatars.githubusercontent.com/u/3596800?v=4',
                                'https://avatars.githubusercontent.com/u/881938?v=4',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->id('imageLimited')
                    ->schema([
                        ImageEntry::make('colleagues')
                            ->circular()
                            ->height(40)
                            ->limit(3)
                            ->stacked()
                            ->state([
                                'https://avatars.githubusercontent.com/u/41837763?v=4',
                                'https://avatars.githubusercontent.com/u/44533235?v=4',
                                'https://avatars.githubusercontent.com/u/22632550?v=4',
                                'https://avatars.githubusercontent.com/u/3596800?v=4',
                                'https://avatars.githubusercontent.com/u/881938?v=4',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->id('imageLimitedRemainingText')
                    ->schema([
                        ImageEntry::make('colleagues')
                            ->circular()
                            ->height(40)
                            ->limit(3)
                            ->limitedRemainingText()
                            ->stacked()
                            ->state([
                                'https://avatars.githubusercontent.com/u/41837763?v=4',
                                'https://avatars.githubusercontent.com/u/44533235?v=4',
                                'https://avatars.githubusercontent.com/u/22632550?v=4',
                                'https://avatars.githubusercontent.com/u/3596800?v=4',
                                'https://avatars.githubusercontent.com/u/881938?v=4',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-3xl',
                    ])
                    ->id('imageLimitedRemainingTextSeparately')
                    ->schema([
                        ImageEntry::make('colleagues')
                            ->circular()
                            ->height(40)
                            ->limit(3)
                            ->limitedRemainingText(isSeparate: true)
                            ->stacked()
                            ->state([
                                'https://avatars.githubusercontent.com/u/41837763?v=4',
                                'https://avatars.githubusercontent.com/u/44533235?v=4',
                                'https://avatars.githubusercontent.com/u/22632550?v=4',
                                'https://avatars.githubusercontent.com/u/3596800?v=4',
                                'https://avatars.githubusercontent.com/u/881938?v=4',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('color')
                    ->schema([
                        ColorEntry::make('color')
                            ->state('#3490dc'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('colorCopyable')
                    ->schema([
                        ColorEntry::make('color')
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500)
                            ->copyable()
                            ->state('#3490dc'),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('code')
                    ->schema([
                        CodeEntry::make('code')
                            ->grammar(Grammar::Php)
                            ->state(<<<PHP
                                <?php

                                namespace App\Models;

                                use Illuminate\Database\Eloquent\Model;

                                class Post extends Model
                                {
                                    // ...
                                }
                                PHP),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('codeDracula')
                    ->schema([
                        CodeEntry::make('code')
                            ->darkTheme(Theme::Dracula)
                            ->grammar(Grammar::Php)
                            ->lightTheme(Theme::Dracula)
                            ->state(<<<PHP
                                <?php

                                namespace App\Models;

                                use Illuminate\Database\Eloquent\Model;

                                class Post extends Model
                                {
                                    // ...
                                }
                                PHP),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('keyValue')
                    ->schema([
                        KeyValueEntry::make('meta')
                            ->state([
                                'description' => 'Filament is a collection of Laravel packages',
                                'og:type' => 'website',
                                'og:site_name' => 'Filament',
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('repeatable')
                    ->schema([
                        RepeatableEntry::make('comments')
                            ->columns(2)
                            ->schema([
                                TextEntry::make('author'),
                                TextEntry::make('title'),
                                TextEntry::make('content')
                                    ->columnSpan(2),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('repeatableGrid')
                    ->schema([
                        RepeatableEntry::make('comments')
                            ->columns(2)
                            ->grid(2)
                            ->schema([
                                TextEntry::make('author'),
                                TextEntry::make('title'),
                                TextEntry::make('content')
                                    ->columnSpan(2),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-5xl',
                    ])
                    ->id('repeatableTable')
                    ->schema([
                        RepeatableEntry::make('comments')
                            ->schema([
                                TextEntry::make('author'),
                                TextEntry::make('title'),
                                IconEntry::make('is_published')
                                    ->boolean(),
                            ])
                            ->table([
                                TableColumn::make('Author'),
                                TableColumn::make('Title'),
                                TableColumn::make('Published'),
                            ]),
                    ]),
                Group::make()
                    ->extraAttributes([
                        'class' => 'p-16 max-w-xl',
                    ])
                    ->id('suffixAction')
                    ->schema([
                        TextEntry::make('suffixAction')
                            ->label('Cost')
                            ->prefix('€')
                            ->suffixAction(
                                Action::make('copyCostToPrice')
                                    ->icon(Heroicon::Clipboard),
                            )
                            ->default('22.66'),
                    ]),
            ])
            ->constantState([
                'comments' => [
                    [
                        'author' => ['name' => 'Jane Doe'],
                        'title' => 'Wow!',
                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.',
                        'is_published' => true,
                    ],
                    [
                        'author' => ['name' => 'John Doe'],
                        'title' => 'This isn\'t working. Help!',
                        'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam euismod, nisl eget aliquam ultricies, nunc nisl aliquet nunc, quis aliquam nisl.',
                        'is_published' => false,
                    ],
                ],
            ]);
    }

    public function render()
    {
        return view('livewire.infolists.entries');
    }
}
