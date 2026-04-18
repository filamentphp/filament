<?php

namespace App\Livewire\Infolists\Entries;

use Filament\Infolists\Components\CodeEntry;
use Filament\Schemas\Components\Group;
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
