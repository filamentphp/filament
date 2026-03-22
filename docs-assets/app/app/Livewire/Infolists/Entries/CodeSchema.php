<?php

namespace App\Livewire\Infolists\Entries;

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
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\FontFamily;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\IconPosition;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;
use Phiki\Grammar\Grammar;
use Phiki\Theme\Theme;

class CodeSchema
{
    public static function schema(): array
    {
        return [
            Group::make()
                ->id('code')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
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
                ->id('codeDracula')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CodeEntry::make('code')
                        ->grammar(Grammar::Php)
                        ->lightTheme(Theme::Dracula)
                        ->darkTheme(Theme::Dracula)
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
                ->id('codeJavascript')
                ->extraAttributes([
                    'class' => 'p-16 max-w-xl',
                ])
                ->schema([
                    CodeEntry::make('code')
                        ->grammar(Grammar::Javascript)
                        ->state(<<<'JS'
                            import { createApp } from 'vue'

                            const app = createApp({
                                data() {
                                    return {
                                        message: 'Hello World',
                                        count: 0,
                                    }
                                },
                                methods: {
                                    increment() {
                                        this.count++
                                    },
                                },
                            })

                            app.mount('#app')
                            JS),
                ]),
        ];
    }
}
