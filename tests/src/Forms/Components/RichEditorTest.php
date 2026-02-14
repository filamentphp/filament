<?php

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\HasToolbarButtons;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\RichContentPlugin;
use Filament\Forms\Components\RichEditor\RichEditorTool;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Validation\ValidationException;
use Tiptap\Core\Extension;

uses(TestCase::class);

test('fields can be required', function (): void {
    $errors = [];

    try {
        Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                $field = (new RichEditor('content'))
                    ->required(),
            ])
            ->fill([
                'content' => '',
            ])
            ->validate();
    } catch (ValidationException $exception) {
        $errors = $exception->validator->errors()->get($field->getStatePath());
    }

    expect($errors)
        ->toContain('The content field is required.');
});

test('can get default toolbar buttons using `getDefaultToolbarButtons()`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ])
        ->getComponents()[0];

    $defaultButtons = $richEditor->getDefaultToolbarButtons();

    expect($defaultButtons)
        ->toBeArray()
        ->toHaveCount(5)
        ->and($defaultButtons[0])->toEqual(['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'])
        ->and($defaultButtons[1])->toEqual(['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd'])
        ->and($defaultButtons[2])->toEqual(['blockquote', 'codeBlock', 'bulletList', 'orderedList'])
        ->and($defaultButtons[3])->toEqual(['table', 'attachFiles'])
        ->and($defaultButtons[4])->toEqual(['undo', 'redo']);
});

test('can overwrite toolbar buttons array using `toolbarButtons()`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->toolbarButtons([
                    ['bold', 'italic'],
                    ['undo', 'redo'],
                ]),
        ])
        ->getComponents()[0];

    $buttons = $richEditor->getToolbarButtons();

    expect($buttons)
        ->toBeArray()
        ->toHaveCount(2)
        ->and($buttons[0])->toEqual(['bold', 'italic'])
        ->and($buttons[1])->toEqual(['undo', 'redo']);
});

test('can overwrite toolbar buttons with closure using `toolbarButtons()`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->toolbarButtons(fn () => [
                    ['bold', 'italic'],
                ]),
        ])
        ->getComponents()[0];

    $buttons = $richEditor->getToolbarButtons();

    expect($buttons)
        ->toBeArray()
        ->toHaveCount(1)
        ->and($buttons[0])->toEqual(['bold', 'italic']);
});

test('can disable specific toolbar buttons using `disableToolbarButtons()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->disableToolbarButtons(['bold', 'italic', 'attachFiles']);

    $buttons = $richEditor->getToolbarButtons();

    // Check that `bold`, `italic`, and `attachFiles` buttons are not present
    $flatButtons = array_merge(...$buttons);

    expect($flatButtons)
        ->not->toContain('bold')
        ->not->toContain('italic')
        ->not->toContain('attachFiles')
        ->toContain('underline')
        ->toContain('strike')
        ->toContain('undo');
});

test('can enable additional toolbar buttons using `enableToolbarButtons()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->enableToolbarButtons(['h1', 'textColor']);

    $buttons = $richEditor->getToolbarButtons();

    // Check that all default buttons plus `h1` and `textColor` are present
    $flatButtons = array_merge(...$buttons);

    expect($flatButtons)
        ->toContain('bold')
        ->toContain('italic')
        ->toContain('h1')
        ->toContain('textColor');
});

test('can disable all toolbar buttons using `disableAllToolbarButtons()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->disableAllToolbarButtons();

    $buttons = $richEditor->getToolbarButtons();

    expect($buttons)->toBeArray()->toBeEmpty();
});

test('can conditionally disable all toolbar buttons using `disableAllToolbarButtons()`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->disableAllToolbarButtons(false),
        ])
        ->getComponents()[0];

    $buttons = $richEditor->getToolbarButtons();

    expect($buttons)->toBeArray()->not->toBeEmpty();
});

test('can check if toolbar button exists using `hasToolbarButton()`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ])
        ->getComponents()[0];

    expect($richEditor->hasToolbarButton('bold'))->toBeTrue()
        ->and($richEditor->hasToolbarButton('italic'))->toBeTrue()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeTrue()
        ->and($richEditor->hasToolbarButton('nonexistent'))->toBeFalse();
});

test('can check if toolbar button exists with array using `hasToolbarButton()`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ])
        ->getComponents()[0];

    expect($richEditor->hasToolbarButton(['bold', 'italic']))->toBeTrue()
        ->and($richEditor->hasToolbarButton(['nonexistent1', 'nonexistent2']))->toBeFalse()
        ->and($richEditor->hasToolbarButton(['bold', 'nonexistent']))->toBeTrue(); // At least one exists
});

test('can check if custom toolbar buttons are set using `hasCustomToolbarButtons()`', function (): void {
    $richEditor = RichEditor::make('content');

    expect($richEditor->hasCustomToolbarButtons())->toBeFalse();

    $richEditor->toolbarButtons([['bold', 'italic']]);

    expect($richEditor->hasCustomToolbarButtons())->toBeTrue();
});

test('toolbar buttons are properly grouped by `getToolbarButtons()`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->toolbarButtons([
                    ['bold', 'italic'],
                    'underline',
                    'strike',
                ]),
        ])
        ->getComponents()[0];

    $buttons = $richEditor->getToolbarButtons();

    // The `getToolbarButtons()` method groups consecutive non-array buttons together.
    // When an array is encountered, it becomes its own group, and any preceding
    // non-array buttons are grouped into their own group at the end.
    expect($buttons)
        ->toBeArray()
        ->toHaveCount(2)
        ->and($buttons[0])->toEqual(['bold', 'italic'])
        ->and($buttons[1])->toEqual(['underline', 'strike']);
});

test('blank button groups are filtered out by `getToolbarButtons()`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->toolbarButtons([
                    ['bold', 'italic'],
                    [],
                    ['undo', 'redo'],
                ]),
        ])
        ->getComponents()[0];

    $buttons = $richEditor->getToolbarButtons();

    expect($buttons)
        ->toBeArray()
        ->toHaveCount(2)
        ->and($buttons[0])->toEqual(['bold', 'italic'])
        ->and($buttons[1])->toEqual(['undo', 'redo']);
});

test('cannot use `disableToolbarButtons()` when using closure', function (): void {
    $richEditor = RichEditor::make('content')
        ->toolbarButtons(fn () => [['bold', 'italic']]);

    expect(fn () => $richEditor->disableToolbarButtons(['bold']))
        ->toThrow(LogicException::class, 'You cannot use the `disableToolbarButtons()` method when the toolbar buttons are dynamically returned from a function.');
});

test('cannot use `enableToolbarButtons()` when using closure', function (): void {
    $richEditor = RichEditor::make('content')
        ->toolbarButtons(fn () => [['bold', 'italic']]);

    expect(fn () => $richEditor->enableToolbarButtons(['underline']))
        ->toThrow(LogicException::class, 'You cannot use the `enableToolbarButtons()` method when the toolbar buttons are dynamically returned from a function.');
});

test('`hasFileAttachments()` returns true by default', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ])
        ->getComponents()[0];

    expect($richEditor->hasFileAttachments())->toBeTrue()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeTrue();
});

test('`hasFileAttachments()` returns false when `attachFiles` button is removed using `disableToolbarButtons()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->disableToolbarButtons(['attachFiles']);

    expect($richEditor->hasFileAttachments())->toBeFalse()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeFalse();
});

test('`hasFileAttachments()` returns true when `attachFiles` is in custom toolbar buttons', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->toolbarButtons([
                    ['bold', 'italic'],
                    ['attachFiles'],
                ]),
        ])
        ->getComponents()[0];

    expect($richEditor->hasFileAttachments())->toBeTrue()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeTrue();
});

test('`hasFileAttachments()` returns false with custom toolbar buttons without `attachFiles`', function (): void {
    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->toolbarButtons([
                    ['bold', 'italic'],
                    ['undo', 'redo'],
                ]),
        ])
        ->getComponents()[0];

    expect($richEditor->hasFileAttachments())->toBeFalse()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeFalse();
});

test('`fileAttachments()` method takes precedence over `disableToolbarButtons()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->disableToolbarButtons(['bold']);
    $richEditor->fileAttachments(false);

    expect($richEditor->hasFileAttachments())->toBeFalse()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeFalse();

    $buttons = $richEditor->getToolbarButtons();
    $flatButtons = array_merge(...$buttons);

    expect($flatButtons)->not->toContain('attachFiles');
});

test('`fileAttachments(false)` works when called before `disableToolbarButtons()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->fileAttachments(false);
    $richEditor->disableToolbarButtons(['bold']);

    expect($richEditor->hasFileAttachments())->toBeFalse()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeFalse();

    $buttons = $richEditor->getToolbarButtons();
    $flatButtons = array_merge(...$buttons);

    expect($flatButtons)->not->toContain('attachFiles');
});

test('`disableToolbarButtons()` with `attachFiles` also makes `hasFileAttachments()` return false', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->disableToolbarButtons(['attachFiles']);

    expect($richEditor->hasFileAttachments())->toBeFalse()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeFalse();
});

test('`fileAttachments(true)` does not force `attachFiles` button to appear when using `disableToolbarButtons()`', function (): void {
    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->disableToolbarButtons(['attachFiles']);
    $richEditor->fileAttachments(true);

    // File attachments are enabled (drag/drop works), but the toolbar button remains hidden
    expect($richEditor->hasFileAttachments())->toBeTrue()
        ->and($richEditor->hasToolbarButton('attachFiles'))->toBeFalse();
});

test('plugin implementing `HasToolbarButtons` can enable toolbar buttons', function (): void {
    $plugin = new class implements HasToolbarButtons, RichContentPlugin
    {
        /** @return array<Extension> */
        public function getTipTapPhpExtensions(): array
        {
            return [];
        }

        /** @return array<string> */
        public function getTipTapJsExtensions(): array
        {
            return [];
        }

        /** @return array<RichEditorTool> */
        public function getEditorTools(): array
        {
            return [];
        }

        /** @return array<Action> */
        public function getEditorActions(): array
        {
            return [];
        }

        /** @return array<string | array<string | array<string>>> */
        public function getEnabledToolbarButtons(): array
        {
            return ['highlight'];
        }

        /** @return array<string> */
        public function getDisabledToolbarButtons(): array
        {
            return [];
        }
    };

    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->plugins([$plugin]),
        ])
        ->getComponents()[0];

    $flatButtons = array_merge(...$richEditor->getToolbarButtons());

    expect($flatButtons)
        ->toContain('highlight')
        ->toContain('bold');
});

test('plugin implementing `HasToolbarButtons` can disable toolbar buttons', function (): void {
    $plugin = new class implements HasToolbarButtons, RichContentPlugin
    {
        /** @return array<Extension> */
        public function getTipTapPhpExtensions(): array
        {
            return [];
        }

        /** @return array<string> */
        public function getTipTapJsExtensions(): array
        {
            return [];
        }

        /** @return array<RichEditorTool> */
        public function getEditorTools(): array
        {
            return [];
        }

        /** @return array<Action> */
        public function getEditorActions(): array
        {
            return [];
        }

        /** @return array<string | array<string | array<string>>> */
        public function getEnabledToolbarButtons(): array
        {
            return [];
        }

        /** @return array<string> */
        public function getDisabledToolbarButtons(): array
        {
            return ['bold', 'italic'];
        }
    };

    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->plugins([$plugin]),
        ])
        ->getComponents()[0];

    $flatButtons = array_merge(...$richEditor->getToolbarButtons());

    expect($flatButtons)
        ->not->toContain('bold')
        ->not->toContain('italic')
        ->toContain('underline')
        ->toContain('undo');
});

test('user `disableToolbarButtons()` overrides plugin-enabled toolbar buttons', function (): void {
    $plugin = new class implements HasToolbarButtons, RichContentPlugin
    {
        /** @return array<Extension> */
        public function getTipTapPhpExtensions(): array
        {
            return [];
        }

        /** @return array<string> */
        public function getTipTapJsExtensions(): array
        {
            return [];
        }

        /** @return array<RichEditorTool> */
        public function getEditorTools(): array
        {
            return [];
        }

        /** @return array<Action> */
        public function getEditorActions(): array
        {
            return [];
        }

        /** @return array<string | array<string | array<string>>> */
        public function getEnabledToolbarButtons(): array
        {
            return ['highlight'];
        }

        /** @return array<string> */
        public function getDisabledToolbarButtons(): array
        {
            return [];
        }
    };

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->plugins([$plugin]),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->disableToolbarButtons(['highlight']);

    $flatButtons = array_merge(...$richEditor->getToolbarButtons());

    expect($flatButtons)
        ->not->toContain('highlight')
        ->toContain('bold');
});

test('user `enableToolbarButtons()` overrides plugin-disabled toolbar buttons', function (): void {
    $plugin = new class implements HasToolbarButtons, RichContentPlugin
    {
        /** @return array<Extension> */
        public function getTipTapPhpExtensions(): array
        {
            return [];
        }

        /** @return array<string> */
        public function getTipTapJsExtensions(): array
        {
            return [];
        }

        /** @return array<RichEditorTool> */
        public function getEditorTools(): array
        {
            return [];
        }

        /** @return array<Action> */
        public function getEditorActions(): array
        {
            return [];
        }

        /** @return array<string | array<string | array<string>>> */
        public function getEnabledToolbarButtons(): array
        {
            return [];
        }

        /** @return array<string> */
        public function getDisabledToolbarButtons(): array
        {
            return ['bold'];
        }
    };

    $schema = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->plugins([$plugin]),
        ]);

    $richEditor = $schema->getComponents()[0];
    $richEditor->enableToolbarButtons(['bold']);

    $flatButtons = array_merge(...$richEditor->getToolbarButtons());

    expect($flatButtons)
        ->toContain('bold');
});

test('plugin without `HasToolbarButtons` does not affect toolbar buttons', function (): void {
    $plugin = new class implements RichContentPlugin
    {
        /** @return array<Extension> */
        public function getTipTapPhpExtensions(): array
        {
            return [];
        }

        /** @return array<string> */
        public function getTipTapJsExtensions(): array
        {
            return [];
        }

        /** @return array<RichEditorTool> */
        public function getEditorTools(): array
        {
            return [];
        }

        /** @return array<Action> */
        public function getEditorActions(): array
        {
            return [];
        }
    };

    $richEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content')
                ->plugins([$plugin]),
        ])
        ->getComponents()[0];

    $defaultRichEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            RichEditor::make('content'),
        ])
        ->getComponents()[0];

    expect($richEditor->getToolbarButtons())
        ->toEqual($defaultRichEditor->getToolbarButtons());
});
