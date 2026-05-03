<?php

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\Plugins\Contracts\HasFileAttachmentProvider;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\RichEditor\RichContentRenderer;
use Filament\Forms\Components\RichEditor\StateCasts\RichEditorStateCast;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Forms\RichEditor\PluginWithFileAttachmentProvider;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\Post;
use Filament\Tests\Fixtures\Models\PostWithRichContent;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\Fixtures\RichEditor\TestRichContentPlugin;
use Filament\Tests\Fixtures\RichEditor\TestRichContentPluginWithoutToolbarButtons;
use Filament\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

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

describe('toolbar buttons', function (): void {
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
            ->toHaveCount(6)
            ->and($defaultButtons[0])->toEqual(['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'])
            ->and($defaultButtons[1])->toEqual(['h2', 'h3'])
            ->and($defaultButtons[2])->toEqual(['alignStart', 'alignCenter', 'alignEnd'])
            ->and($defaultButtons[3])->toEqual(['blockquote', 'codeBlock', 'bulletList', 'orderedList'])
            ->and($defaultButtons[4])->toEqual(['table', 'attachFiles'])
            ->and($defaultButtons[5])->toEqual(['undo', 'redo']);
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

    test('can disable grouped toolbar buttons using `disableToolbarButtons()`', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->toolbarButtons([
                        [ToolbarButtonGroup::make('Heading', ['h1', 'h2'])],
                        ['undo', 'redo'],
                    ]),
            ])
            ->getComponents()[0];

        $richEditor->disableToolbarButtons(['h1']);

        $headingButtonGroup = $richEditor->getToolbarButtons()[0][0];

        expect($headingButtonGroup)->toBeInstanceOf(ToolbarButtonGroup::class);

        expect($headingButtonGroup->getButtons())->toEqual(['h2'])
            ->and($richEditor->hasToolbarButton('h1'))->toBeFalse()
            ->and($richEditor->hasToolbarButton('h2'))->toBeTrue();
    });

});

describe('file attachments', function (): void {
    test('`hasFileAttachments()` returns `true` by default', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content'),
            ])
            ->getComponents()[0];

        expect($richEditor->hasFileAttachments())->toBeTrue()
            ->and($richEditor->hasToolbarButton('attachFiles'))->toBeTrue();
    });

    test('`hasFileAttachments()` returns `false` when `attachFiles` button is removed using `disableToolbarButtons()`', function (): void {
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

    test('`hasFileAttachments()` returns `true` when `attachFiles` is in custom toolbar buttons', function (): void {
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

    test('`hasFileAttachments()` returns `false` with custom toolbar buttons without `attachFiles`', function (): void {
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

});

describe('plugins', function (): void {
    test('plugin implementing `HasToolbarButtons` can enable toolbar buttons', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->plugins([new TestRichContentPlugin(enabledButtons: ['highlight'])]),
            ])
            ->getComponents()[0];

        $flatButtons = array_merge(...$richEditor->getToolbarButtons());

        expect($flatButtons)
            ->toContain('highlight')
            ->toContain('bold');
    });

    test('plugin implementing `HasToolbarButtons` does not duplicate toolbar buttons already returned by `toolbarButtons()`', function (): void {
        $buttons = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->toolbarButtons([
                        ['bold', 'highlight'],
                        ['undo', 'redo'],
                    ])
                    ->plugins([new TestRichContentPlugin(enabledButtons: ['highlight'])]),
            ])
            ->getComponents()[0]
            ->getToolbarButtons();

        $flatButtons = array_merge(...$buttons);
        $highlightButtons = array_values(array_filter($flatButtons, fn (mixed $button): bool => $button === 'highlight'));

        expect($buttons)
            ->toHaveCount(2)
            ->and($buttons[0])->toEqual(['bold', 'highlight'])
            ->and($buttons[1])->toEqual(['undo', 'redo'])
            ->and($highlightButtons)->toHaveCount(1);
    });

    test('plugin implementing `HasToolbarButtons` does not duplicate toolbar buttons already returned by `ToolbarButtonGroup` in `toolbarButtons()`', function (): void {
        $buttons = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->toolbarButtons([
                        ['bold', 'italic'],
                        [ToolbarButtonGroup::make('Formatting', ['highlight', 'underline'])],
                        ['undo', 'redo'],
                    ])
                    ->plugins([new TestRichContentPlugin(enabledButtons: ['highlight'])]),
            ])
            ->getComponents()[0]
            ->getToolbarButtons();

        $highlightButtonGroup = $buttons[1][0];

        expect($buttons)->toHaveCount(3)
            ->and($highlightButtonGroup)->toBeInstanceOf(ToolbarButtonGroup::class)
            ->and($highlightButtonGroup->getButtons())->toEqual(['highlight', 'underline'])
            ->and($buttons[2])->toEqual(['undo', 'redo']);
    });

    test('plugin implementing `HasToolbarButtons` does not duplicate toolbar buttons already enabled by user `enableToolbarButtons()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->plugins([new TestRichContentPlugin(enabledButtons: ['highlight'])]),
            ]);

        $richEditor = $schema->getComponents()[0];
        $richEditor->enableToolbarButtons(['highlight']);

        $flatButtons = array_merge(...$richEditor->getToolbarButtons());
        $highlightButtons = array_values(array_filter($flatButtons, fn (mixed $button): bool => $button === 'highlight'));

        expect($highlightButtons)
            ->toHaveCount(1);
    });

    test('plugin implementing `HasToolbarButtons` can disable toolbar buttons', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->plugins([new TestRichContentPlugin(disabledButtons: ['bold', 'italic'])]),
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
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->plugins([new TestRichContentPlugin(enabledButtons: ['highlight'])]),
            ]);

        $richEditor = $schema->getComponents()[0];
        $richEditor->disableToolbarButtons(['highlight']);

        $flatButtons = array_merge(...$richEditor->getToolbarButtons());

        expect($flatButtons)
            ->not->toContain('highlight')
            ->toContain('bold');
    });

    test('user `enableToolbarButtons()` overrides plugin-disabled toolbar buttons', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->plugins([new TestRichContentPlugin(disabledButtons: ['bold'])]),
            ]);

        $richEditor = $schema->getComponents()[0];
        $richEditor->enableToolbarButtons(['bold']);

        $flatButtons = array_merge(...$richEditor->getToolbarButtons());

        expect($flatButtons)
            ->toContain('bold');
    });

    test('plugin without `HasToolbarButtons` does not affect toolbar buttons', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->plugins([new TestRichContentPluginWithoutToolbarButtons]),
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

    test('rich content attribute resolves file attachment provider from plugin implementing `HasFileAttachmentProvider` without calling `fileAttachmentProvider()`', function (): void {
        $record = new PostWithRichContent;

        $contentAttribute = $record->getRichContentAttribute('content');

        $pluginWithProvider = $contentAttribute->getPlugins()[0];

        expect($pluginWithProvider)
            ->toBeInstanceOf(HasFileAttachmentProvider::class);

        $expectedProvider = $pluginWithProvider->getFileAttachmentProvider();

        expect($contentAttribute->getFileAttachmentProvider())
            ->toBe($expectedProvider);
    });

    test('RichEditor receives file attachment provider from rich content attribute when attribute resolves it from plugin', function (): void {
        $record = new PostWithRichContent;

        $contentAttribute = $record->getRichContentAttribute('content');

        $expectedProvider = $contentAttribute->getPlugins()[0]->getFileAttachmentProvider();

        $schema = Schema::make(Livewire::make())
            ->model($record)
            ->statePath('data')
            ->components([
                RichEditor::make('content'),
            ]);

        $richEditor = $schema->getComponents()[0];

        expect($richEditor->getContentAttribute())
            ->not->toBeNull()
            ->getFileAttachmentProvider()->toBe($expectedProvider);

        expect($richEditor->getFileAttachmentProvider())
            ->toBe($expectedProvider);
    });

    test('`RichContentRenderer` resolves file attachment provider from plugin implementing `HasFileAttachmentProvider`', function (): void {
        $plugin = PluginWithFileAttachmentProvider::make();

        $renderer = RichContentRenderer::make()
            ->plugins([$plugin]);

        expect($renderer->getFileAttachmentProvider())
            ->toBe($plugin->getFileAttachmentProvider());
    });

});

describe('list item wrapping', function (): void {
    test('list items with bare text content are wrapped in paragraphs on fill', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')->json(),
            ])
            ->fill([
                'content' => '<ul><li>First item</li><li>Second item</li></ul>',
            ]);

        $state = $schema->getState()['content'];

        $bulletList = collect($state['content'])->firstWhere('type', 'bulletList');

        expect($bulletList)->not->toBeNull();

        foreach ($bulletList['content'] as $listItem) {
            expect($listItem['type'])->toBe('listItem');
            expect($listItem['content'][0]['type'])->toBe('paragraph');
        }

        $firstParagraph = $bulletList['content'][0]['content'][0];
        $secondParagraph = $bulletList['content'][1]['content'][0];

        expect($firstParagraph['content'][0]['text'])->toBe('First item');
        expect($secondParagraph['content'][0]['text'])->toBe('Second item');
    });

    test('list items with marked text content are wrapped in paragraphs on fill', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')->json(),
            ])
            ->fill([
                'content' => '<ul><li><strong>Bold item</strong> with text</li></ul>',
            ]);

        $state = $schema->getState()['content'];

        $bulletList = collect($state['content'])->firstWhere('type', 'bulletList');
        $firstLi = $bulletList['content'][0];
        $paragraph = $firstLi['content'][0];

        expect($paragraph['type'])->toBe('paragraph');

        $boldText = $paragraph['content'][0];
        $plainText = $paragraph['content'][1];

        expect($boldText['type'])->toBe('text');
        expect($boldText['text'])->toBe('Bold item');
        expect($boldText['marks'][0]['type'])->toBe('bold');

        expect($plainText['type'])->toBe('text');
        expect($plainText['text'])->toBe(' with text');
        expect($plainText)->not->toHaveKey('marks');
    });

    test('list items already containing paragraphs are not double-wrapped on fill', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')->json(),
            ])
            ->fill([
                'content' => '<ul><li><p>Already wrapped</p></li></ul>',
            ]);

        $state = $schema->getState()['content'];

        $bulletList = collect($state['content'])->firstWhere('type', 'bulletList');
        $firstLi = $bulletList['content'][0];

        expect($firstLi['content'][0]['type'])->toBe('paragraph');
        expect($firstLi['content'][0]['content'][0]['type'])->toBe('text');
        expect($firstLi['content'][0]['content'][0]['text'])->toBe('Already wrapped');
    });
});

describe('link HTML attributes', function (): void {
    test('links without explicit HTML attributes do not gain them during HTML round-trip', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content'),
            ])
            ->fill([
                'content' => '<p><a href="https://example.com">Link</a></p>',
            ]);

        expect($schema->getState()['content'])
            ->toContain('href="https://example.com"')
            ->not->toContain('target=')
            ->not->toContain('rel=');
    });

    test('links with explicit HTML attributes preserve them during HTML round-trip', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content'),
            ])
            ->fill([
                'content' => '<p><a href="https://example.com" target="_blank" rel="noopener noreferrer">Link</a></p>',
            ]);

        expect($schema->getState()['content'])
            ->toContain('href="https://example.com"')
            ->toContain('target="_blank"')
            ->toContain('rel="noopener noreferrer"');
    });
});

it('can set `mergeTags()`', function (): void {
    $editor = RichEditor::make('content')
        ->mergeTags(['{{name}}', '{{email}}']);

    expect($editor->getMergeTags())->toHaveKeys(['{{name}}', '{{email}}']);
});

it('can set `linkProtocols()`', function (): void {
    $editor = RichEditor::make('content')
        ->linkProtocols(['https', 'mailto']);

    expect($editor->getLinkProtocols())->toBe(['https', 'mailto']);
});

it('can set `textColors()`', function (): void {
    $editor = RichEditor::make('content')
        ->textColors(['red' => '#ff0000', 'blue' => '#0000ff']);

    $colors = $editor->getTextColors();

    expect($colors)->toHaveKeys(['red', 'blue']);
});

it('can set `resizableImages()`', function (): void {
    $editor = RichEditor::make('content');

    expect($editor->hasResizableImages())->toBeFalse();

    $editor->resizableImages();

    expect($editor->hasResizableImages())->toBeTrue();
});

it('can set `activePanel()`', function (): void {
    $editor = RichEditor::make('content');

    expect($editor->getActivePanel())->toBeNull();

    $editor->activePanel('merge-tags');

    expect($editor->getActivePanel())->toBe('merge-tags');
});

it('can set `uploadingFileMessage()`', function (): void {
    $editor = RichEditor::make('content');

    expect($editor->getUploadingFileMessage())->toBeString();
    expect($editor->getUploadingFileMessage())->not->toBeEmpty();

    $editor->uploadingFileMessage('Uploading file...');

    expect($editor->getUploadingFileMessage())->toBe('Uploading file...');
});

it('returns fluent `$this` from `customBlocks()`', function (): void {
    $editor = RichEditor::make('content');

    $result = $editor->customBlocks(['App\\CustomBlock']);

    expect($result)->toBe($editor);
});

it('can set `floatingToolbars()`', function (): void {
    $editor = RichEditor::make('content');

    $result = $editor->floatingToolbars(['text' => ['bold', 'italic']]);

    expect($result)->toBe($editor);
});

it('can set `noMergeTagSearchResultsMessage()`', function (): void {
    $editor = RichEditor::make('content');

    expect($editor->getNoMergeTagSearchResultsMessage())->toBeString();

    $editor->noMergeTagSearchResultsMessage('No tags found');

    expect($editor->getNoMergeTagSearchResultsMessage())->toBe('No tags found');
});

it('returns `false` for `hasMentions()` by default', function (): void {
    $editor = RichEditor::make('content');

    expect($editor->hasMentions())->toBeFalse();
});

it('returns fluent `$this` from `tools()`', function (): void {
    $editor = RichEditor::make('content');

    $result = $editor->tools(['bold', 'italic']);

    expect($result)->toBe($editor);
});

it('can set `mergeTags()` with a `Closure`', function (): void {
    $editor = RichEditor::make('content')
        ->mergeTags(static fn (): array => ['{{name}}', '{{email}}']);

    expect($editor->getMergeTags())->toHaveKeys(['{{name}}', '{{email}}']);
});

it('can set `linkProtocols()` with a `Closure`', function (): void {
    $editor = RichEditor::make('content')
        ->linkProtocols(static fn (): array => ['https', 'tel']);

    expect($editor->getLinkProtocols())->toBe(['https', 'tel']);
});

it('can set `activePanel()` with a `Closure`', function (): void {
    $editor = RichEditor::make('content')
        ->activePanel(static fn (): string => 'custom-panel');

    expect($editor->getActivePanel())->toBe('custom-panel');
});

it('can clear `activePanel()` with `null`', function (): void {
    $editor = RichEditor::make('content')
        ->activePanel('merge-tags')
        ->activePanel(null);

    expect($editor->getActivePanel())->toBeNull();
});

it('returns fluent `$this` from `customTextColors()`', function (): void {
    $editor = RichEditor::make('content');

    $result = $editor->customTextColors();

    expect($result)->toBe($editor);
});

it('returns fluent `$this` from `plugins()`', function (): void {
    $editor = RichEditor::make('content');

    $result = $editor->plugins([]);

    expect($result)->toBe($editor);
});

describe('JSON mode', function (): void {
    it('can set `json()`', function (): void {
        $editor = RichEditor::make('content')->json();

        expect($editor->isJson())->toBeTrue();
    });

    it('can set `json()` with a `Closure`', function (): void {
        $editor = RichEditor::make('content')
            ->json(static fn (): bool => true);

        expect($editor->isJson())->toBeTrue();
    });

    it('can set `json()` to `false`', function (): void {
        $editor = RichEditor::make('content')
            ->json()
            ->json(false);

        expect($editor->isJson())->toBeFalse();
    });
});

describe('custom text colors', function (): void {
    it('can set `customTextColors()`', function (): void {
        $editor = RichEditor::make('content')->customTextColors();

        expect($editor->hasCustomTextColors())->toBeTrue();
    });

    it('can set `customTextColors()` with a `Closure`', function (): void {
        $editor = RichEditor::make('content')
            ->customTextColors(static fn (): bool => true);

        expect($editor->hasCustomTextColors())->toBeTrue();
    });

    it('can set `customTextColors()` to `false`', function (): void {
        $editor = RichEditor::make('content')
            ->customTextColors()
            ->customTextColors(false);

        expect($editor->hasCustomTextColors())->toBeFalse();
    });
});

describe('Closure support for collections', function (): void {
    it('can set `customBlocks()` with a `Closure`', function (): void {
        $editor = RichEditor::make('content')
            ->customBlocks(static fn (): array => []);

        expect($editor->getCustomBlocks())->toBe([]);
    });

    it('can set `resizableImages()` with a `Closure`', function (): void {
        $editor = RichEditor::make('content')
            ->resizableImages(static fn (): bool => true);

        expect($editor->hasResizableImages())->toBeTrue();
    });

    it('can set `uploadingFileMessage()` with a `Closure`', function (): void {
        $editor = RichEditor::make('content')
            ->uploadingFileMessage(static fn (): string => 'Uploading...');

        expect($editor->getUploadingFileMessage())->toBe('Uploading...');
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderRichEditor::class)
            ->assertSuccessful();
    });

    it('can render with custom `toolbarButtons()`', function (): void {
        livewire(RenderRichEditorWithToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `toolbarButtons()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `disableToolbarButtons()`', function (): void {
        livewire(RenderRichEditorWithDisabledToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `enableToolbarButtons()`', function (): void {
        livewire(RenderRichEditorWithEnabledToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `disableAllToolbarButtons()`', function (): void {
        livewire(RenderRichEditorWithNoToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `disableAllToolbarButtons(false)`', function (): void {
        livewire(RenderRichEditorWithAllToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `mergeTags()`', function (): void {
        livewire(RenderRichEditorWithMergeTags::class)
            ->assertSuccessful();
    });

    it('can render with `mergeTags()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureMergeTags::class)
            ->assertSuccessful();
    });

    it('can render with `linkProtocols()`', function (): void {
        livewire(RenderRichEditorWithLinkProtocols::class)
            ->assertSuccessful();
    });

    it('can render with `linkProtocols()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureLinkProtocols::class)
            ->assertSuccessful();
    });

    it('can render with `textColors()`', function (): void {
        livewire(RenderRichEditorWithTextColors::class)
            ->assertSuccessful();
    });

    it('can render with `resizableImages()`', function (): void {
        livewire(RenderRichEditorWithResizableImages::class)
            ->assertSuccessful();
    });

    it('can render with `resizableImages()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureResizableImages::class)
            ->assertSuccessful();
    });

    it('can render with `activePanel()`', function (): void {
        livewire(RenderRichEditorWithActivePanel::class)
            ->assertSuccessful();
    });

    it('can render with `activePanel()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureActivePanel::class)
            ->assertSuccessful();
    });

    it('can render with `activePanel(null)`', function (): void {
        livewire(RenderRichEditorWithNullActivePanel::class)
            ->assertSuccessful();
    });

    it('can render with `uploadingFileMessage()`', function (): void {
        livewire(RenderRichEditorWithUploadingFileMessage::class)
            ->assertSuccessful()
            ->assertSeeHtml('Uploading file...');
    });

    it('can render with `uploadingFileMessage()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureUploadingFileMessage::class)
            ->assertSuccessful()
            ->assertSeeHtml('Uploading...');
    });

    it('can render with `json()`', function (): void {
        livewire(RenderRichEditorWithJson::class)
            ->assertSuccessful();
    });

    it('can render with `json()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureJson::class)
            ->assertSuccessful();
    });

    it('can render with `json(false)`', function (): void {
        livewire(RenderRichEditorWithJsonFalse::class)
            ->assertSuccessful();
    });

    it('can render with `customTextColors()`', function (): void {
        livewire(RenderRichEditorWithCustomTextColors::class)
            ->assertSuccessful();
    });

    it('can render with `customTextColors()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureCustomTextColors::class)
            ->assertSuccessful();
    });

    it('can render with `customTextColors(false)`', function (): void {
        livewire(RenderRichEditorWithCustomTextColorsFalse::class)
            ->assertSuccessful();
    });

    it('can render with `customBlocks()` set via `Closure`', function (): void {
        livewire(RenderRichEditorWithClosureCustomBlocks::class)
            ->assertSuccessful();
    });

    it('can render with `noMergeTagSearchResultsMessage()`', function (): void {
        livewire(RenderRichEditorWithNoMergeTagSearchResultsMessage::class)
            ->assertSuccessful();
    });

    it('can render with `fileAttachments(false)`', function (): void {
        livewire(RenderRichEditorWithNoFileAttachments::class)
            ->assertSuccessful();
    });

    it('can render with plugin enabling toolbar buttons', function (): void {
        livewire(RenderRichEditorWithPluginEnabledButtons::class)
            ->assertSuccessful();
    });

    it('can render with plugin disabling toolbar buttons', function (): void {
        livewire(RenderRichEditorWithPluginDisabledButtons::class)
            ->assertSuccessful();
    });
});

describe('custom blocks', function (): void {
    it('returns an empty array when no custom blocks are registered', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content'),
            ])
            ->getComponents()[0];

        expect($richEditor->getCustomBlocks())->toBe([]);
    });

    it('can register custom blocks using `customBlocks()`', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                        RichEditorTestBlockB::class,
                    ]),
            ])
            ->getComponents()[0];

        expect($richEditor->getCustomBlocks())->toBe([
            RichEditorTestBlockA::class,
            RichEditorTestBlockB::class,
        ]);
    });

    it('can register custom blocks using a `Closure`', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks(static fn (): array => [
                        RichEditorTestBlockA::class,
                    ]),
            ])
            ->getComponents()[0];

        expect($richEditor->getCustomBlocks())->toBe([
            RichEditorTestBlockA::class,
        ]);
    });

    it('flattens grouped blocks in `getCustomBlocks()`', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                        'Group' => [
                            RichEditorTestBlockB::class,
                            RichEditorTestBlockC::class,
                        ],
                    ]),
            ])
            ->getComponents()[0];

        expect($richEditor->getCustomBlocks())->toBe([
            RichEditorTestBlockA::class,
            RichEditorTestBlockB::class,
            RichEditorTestBlockC::class,
        ]);
    });

    it('returns a keyed array from `getCachedCustomBlocks()`', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                        RichEditorTestBlockB::class,
                    ]),
            ])
            ->getComponents()[0];

        expect($richEditor->getCachedCustomBlocks())->toBe([
            'block-a' => RichEditorTestBlockA::class,
            'block-b' => RichEditorTestBlockB::class,
        ]);
    });

    it('returns an empty array from `getCachedCustomBlocks()` when no blocks are registered', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content'),
            ])
            ->getComponents()[0];

        expect($richEditor->getCachedCustomBlocks())->toBeEmpty();
    });

    it('can retrieve a custom block by ID using `getCustomBlock()`', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                        RichEditorTestBlockB::class,
                    ]),
            ])
            ->getComponents()[0];

        expect($richEditor->getCustomBlock('block-a'))->toBe(RichEditorTestBlockA::class);
        expect($richEditor->getCustomBlock('block-b'))->toBe(RichEditorTestBlockB::class);
    });

    it('returns `null` from `getCustomBlock()` for an unknown ID', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                    ]),
            ])
            ->getComponents()[0];

        expect($richEditor->getCustomBlock('nonexistent'))->toBeNull();
    });

    it('includes `customBlocks` toolbar button when blocks are registered', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                    ]),
            ])
            ->getComponents()[0];

        $flatButtons = array_merge(...$richEditor->getToolbarButtons());

        expect($flatButtons)->toContain('customBlocks');
    });

    it('does not include `customBlocks` toolbar button when no blocks are registered', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content'),
            ])
            ->getComponents()[0];

        $flatButtons = array_merge(...$richEditor->getToolbarButtons());

        expect($flatButtons)->not->toContain('customBlocks');
    });
});

describe('custom block grouping', function (): void {
    it('returns all blocks in a single ungrouped collection when no groups are used', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                        RichEditorTestBlockB::class,
                    ]),
            ])
            ->getComponents()[0];

        $grouped = $richEditor->getGroupedCustomBlocks();

        expect($grouped)->toHaveCount(1);
        expect($grouped->keys()->first())->toBe('');
        expect($grouped->first()->all())->toBe([
            RichEditorTestBlockA::class,
            RichEditorTestBlockB::class,
        ]);
    });

    it('places ungrouped blocks before grouped blocks', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                        'Group' => [
                            RichEditorTestBlockB::class,
                        ],
                    ]),
            ])
            ->getComponents()[0];

        $grouped = $richEditor->getGroupedCustomBlocks();
        $keys = $grouped->keys()->all();

        expect($keys[0])->toBe('');
        expect($keys[1])->toBe('Group');
        expect($grouped['']->all())->toBe([RichEditorTestBlockA::class]);
    });

    it('groups blocks under string-keyed arrays', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        'Alpha' => [RichEditorTestBlockA::class],
                        'Beta' => [RichEditorTestBlockB::class, RichEditorTestBlockC::class],
                    ]),
            ])
            ->getComponents()[0];

        $grouped = $richEditor->getGroupedCustomBlocks();

        expect($grouped)->toHaveCount(2);
        expect($grouped->has('Alpha'))->toBeTrue();
        expect($grouped->has('Beta'))->toBeTrue();
        expect($grouped['Beta']->all())->toBe([RichEditorTestBlockB::class, RichEditorTestBlockC::class]);
    });

    it('preserves group definition order', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        'Zebras' => [RichEditorTestBlockA::class],
                        'Apples' => [RichEditorTestBlockB::class],
                        'Middle' => [RichEditorTestBlockC::class],
                    ]),
            ])
            ->getComponents()[0];

        $grouped = $richEditor->getGroupedCustomBlocks();
        $keys = $grouped->keys()->all();

        expect($keys[0])->toBe('Zebras');
        expect($keys[1])->toBe('Apples');
        expect($keys[2])->toBe('Middle');
    });

    it('treats numeric-keyed nested arrays as ungrouped', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        [RichEditorTestBlockA::class, RichEditorTestBlockB::class],
                        'Group' => [RichEditorTestBlockC::class],
                    ]),
            ])
            ->getComponents()[0];

        $grouped = $richEditor->getGroupedCustomBlocks();
        $keys = $grouped->keys()->all();

        expect($keys[0])->toBe('');
        expect($keys[1])->toBe('Group');
        expect($grouped['']->all())->toBe([RichEditorTestBlockA::class, RichEditorTestBlockB::class]);
    });

    it('collects all ungrouped blocks together', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content')
                    ->customBlocks([
                        RichEditorTestBlockA::class,
                        'Group' => [RichEditorTestBlockC::class],
                        RichEditorTestBlockB::class,
                    ]),
            ])
            ->getComponents()[0];

        $grouped = $richEditor->getGroupedCustomBlocks();

        expect($grouped['']->all())->toBe([
            RichEditorTestBlockA::class,
            RichEditorTestBlockB::class,
        ]);
    });

    it('returns an empty collection when no blocks are registered', function (): void {
        $richEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                RichEditor::make('content'),
            ])
            ->getComponents()[0];

        $grouped = $richEditor->getGroupedCustomBlocks();

        expect($grouped)->toBeEmpty();
    });
});

it('can render `RichEditor` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/rich-editor-browser-test')
            ->assertSee('Content')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/rich-editor-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

describe('preventing file attachment tampering', function (): void {
    beforeEach(function (): void {
        Storage::fake('local');
        Storage::disk('local')->put('uploads/original.jpg', 'original');
        Storage::disk('local')->put('uploads/evil.jpg', 'evil');
    });

    it('allows a tampered `data-id` to overwrite the record when `preventFileAttachmentPathTampering()` is not used', function (): void {
        $post = Post::factory()->create([
            'content' => '<p>Hello</p><img src="/placeholder" data-id="uploads/original.jpg" />',
        ]);

        livewire(TestComponentWithRichEditorRecord::class, ['record' => $post])
            ->set('data.content', '<p>Hello</p><img src="/placeholder" data-id="uploads/evil.jpg" />')
            ->call('save');

        expect($post->fresh()->content)
            ->toContain('data-id="uploads/evil.jpg"')
            ->and($post->fresh()->content)->not->toContain('data-id="uploads/original.jpg"');
    });

    it('fails validation for a tampered `data-id` when using `preventFileAttachmentPathTampering()`', function (): void {
        $post = Post::factory()->create([
            'content' => '<p>Hello</p><img src="/placeholder" data-id="uploads/original.jpg" />',
        ]);

        livewire(TestComponentWithRichEditorRecordPreventingTampering::class, ['record' => $post])
            ->set('data.content', '<p>Hello</p><img src="/placeholder" data-id="uploads/evil.jpg" />')
            ->call('save')
            ->assertHasFormErrors(['content']);

        expect($post->fresh()->content)
            ->toContain('data-id="uploads/original.jpg"')
            ->and($post->fresh()->content)->not->toContain('uploads/evil.jpg');
    });

    it('leaves an unchanged `data-id` alone when using `preventFileAttachmentPathTampering()`', function (): void {
        $post = Post::factory()->create([
            'content' => '<p>Hello</p><img src="/placeholder" data-id="uploads/original.jpg" />',
        ]);

        livewire(TestComponentWithRichEditorRecordPreventingTampering::class, ['record' => $post])
            ->set('data.content', '<p>Hello</p><img src="/placeholder" data-id="uploads/original.jpg" />')
            ->call('save')
            ->assertHasNoFormErrors();

        expect($post->fresh()->content)->toContain('data-id="uploads/original.jpg"');
    });

    it('keeps a `data-id` that the `allowFilePathUsing` callback approves', function (): void {
        Storage::disk('local')->put('templates/brochure.jpg', 'template');

        $post = Post::factory()->create([
            'content' => '<p>Hello</p><img src="/placeholder" data-id="uploads/original.jpg" />',
        ]);

        livewire(TestComponentWithRichEditorRecordAllowingTemplatePaths::class, ['record' => $post])
            ->set('data.content', '<p>Hello</p><img src="/placeholder" data-id="templates/brochure.jpg" />')
            ->call('save')
            ->assertHasNoFormErrors();

        expect($post->fresh()->content)->toContain('data-id="templates/brochure.jpg"');
    });

    it('fails validation when the `allowFilePathUsing` callback rejects a `data-id`', function (): void {
        $post = Post::factory()->create([
            'content' => '<p>Hello</p><img src="/placeholder" data-id="uploads/original.jpg" />',
        ]);

        livewire(TestComponentWithRichEditorRecordAllowingTemplatePaths::class, ['record' => $post])
            ->set('data.content', '<p>Hello</p><img src="/placeholder" data-id="uploads/evil.jpg" />')
            ->call('save')
            ->assertHasFormErrors(['content']);

        expect($post->fresh()->content)
            ->toContain('data-id="uploads/original.jpg"')
            ->and($post->fresh()->content)->not->toContain('uploads/evil.jpg');
    });

    it('fails validation for all `data-id` values when no record is bound and `preventFileAttachmentPathTampering()` is used', function (): void {
        livewire(TestComponentWithRichEditorPreventingTamperingWithoutRecord::class)
            ->set('data.content', '<p>Hello</p><img src="/placeholder" data-id="uploads/evil.jpg" />')
            ->call('save')
            ->assertHasFormErrors(['content']);
    });

    it('uses a custom validation message when `tampered` is defined in `validationMessages()`', function (): void {
        $post = Post::factory()->create([
            'content' => '<p>Hello</p><img src="/placeholder" data-id="uploads/original.jpg" />',
        ]);

        livewire(TestComponentWithRichEditorRecordPreventingTamperingAndCustomMessage::class, ['record' => $post])
            ->set('data.content', '<p>Hello</p><img src="/placeholder" data-id="uploads/evil.jpg" />')
            ->call('save')
            ->assertHasFormErrors(['content' => 'The content references an image that is not permitted.']);
    });

    it('does not resolve a URL for a tampered `data-id` during state cast hydration', function (): void {
        $post = Post::factory()->create([
            'content' => '<p>Hello</p><img src="/placeholder" data-id="uploads/original.jpg" />',
        ]);

        $editor = (new RichEditor('content'))
            ->fileAttachmentsDisk('local')
            ->preventFileAttachmentPathTampering()
            ->container(Schema::make(Livewire::make())->model($post)->statePath('data'));

        $cast = new RichEditorStateCast($editor);

        $result = $cast->set('<p>Hello</p><img src="http://original" data-id="uploads/original.jpg" /><img src="http://evil" data-id="uploads/evil.jpg" />');

        $decoded = json_decode(json_encode($result), true);

        $srcById = [];

        $walker = function ($nodes) use (&$walker, &$srcById): void {
            foreach ($nodes as $node) {
                if (($node['type'] ?? null) === 'image') {
                    $id = $node['attrs']['id'] ?? null;

                    if (filled($id)) {
                        $srcById[$id] = $node['attrs']['src'] ?? null;
                    }
                }

                if (! empty($node['content'] ?? null)) {
                    $walker($node['content']);
                }
            }
        };

        $walker($decoded['content'] ?? []);

        expect($srcById)->toHaveKey('uploads/original.jpg');
        expect($srcById)->toHaveKey('uploads/evil.jpg');
        expect($srcById['uploads/evil.jpg'])->toBeNull();
    });
});

describe('cross-record file attachment callbacks', function (): void {
    it('returns `null` from `getFileAttachmentUrlFromAnotherRecord()` when no callback is set', function (): void {
        $editor = new RichEditor('content');

        expect($editor->getFileAttachmentUrlFromAnotherRecord('any-id'))->toBeNull();
    });

    it('delegates `getFileAttachmentUrlFromAnotherRecord()` to the registered callback', function (): void {
        $editor = (new RichEditor('content'))
            ->getFileAttachmentUrlFromAnotherRecordUsing(
                static fn (string $file): string => "xr://{$file}",
            );

        expect($editor->getFileAttachmentUrlFromAnotherRecord('shared-1'))->toBe('xr://shared-1');
    });

    it('returns `null` from `saveFileAttachmentFromAnotherRecord()` when no callback is set', function (): void {
        $editor = new RichEditor('content');

        expect($editor->saveFileAttachmentFromAnotherRecord('any-id'))->toBeNull();
    });

    it('delegates `saveFileAttachmentFromAnotherRecord()` to the registered callback', function (): void {
        $editor = (new RichEditor('content'))
            ->saveFileAttachmentFromAnotherRecordUsing(
                static fn (string $file): string => "copied:{$file}",
            );

        expect($editor->saveFileAttachmentFromAnotherRecord('from-other'))->toBe('copied:from-other');
    });
});

describe('`resolveFileAttachmentIds()` behaviour', function (): void {
    it('persists a newly uploaded image and rewrites its `data-id` to the stored path', function (): void {
        Storage::fake('tmp-for-tests');

        $image = imagecreatetruecolor(10, 10);
        ob_start();
        imagejpeg($image);
        $imageContent = ob_get_clean();
        imagedestroy($image);

        $temporaryFileName = TemporaryUploadedFile::generateHashNameWithOriginalNameEmbedded(
            UploadedFile::fake()->image('new-upload.jpg'),
        );
        Storage::disk('tmp-for-tests')->put("livewire-tmp/{$temporaryFileName}", $imageContent);

        $temporaryFile = TemporaryUploadedFile::createFromLivewire($temporaryFileName);

        $editor = (new RichEditor('content'))
            ->container(Schema::make(Livewire::make())->statePath('data'));

        $livewire = $editor->getLivewire();

        data_set(
            $livewire,
            'componentFileAttachments.' . $editor->getStatePath() . '.new-upload-uuid',
            $temporaryFile,
        );

        $editor->saveUploadedFileAttachmentUsing(static fn (): string => 'uploads/persisted.jpg');
        $editor->getFileAttachmentUrlUsing(static fn (string $file): string => "https://cdn.test/{$file}");

        $editor->rawState('<p><img src="placeholder" data-id="new-upload-uuid" /></p>');

        $ids = $editor->resolveFileAttachmentIds();

        expect($ids)->toContain('uploads/persisted.jpg');

        $srcs = [];

        walkEditorRawState($editor, function (array $node) use (&$srcs): void {
            if (($node['type'] ?? null) === 'image') {
                $srcs[$node['attrs']['id'] ?? ''] = $node['attrs']['src'] ?? null;
            }
        });

        expect($srcs)->toHaveKey('uploads/persisted.jpg');
        expect($srcs['uploads/persisted.jpg'])->toBe('https://cdn.test/uploads/persisted.jpg');
    });

    it('keeps an existing disk-resident `data-id` unchanged and records it as in-use', function (): void {
        $editor = (new RichEditor('content'))
            ->container(Schema::make(Livewire::make())->statePath('data'));

        $editor->getFileAttachmentUrlUsing(static fn (string $file): ?string => "https://cdn.test/{$file}");

        $editor->rawState('<p><img src="placeholder" data-id="uploads/kept.jpg" /></p>');

        $ids = $editor->resolveFileAttachmentIds();

        expect($ids)->toContain('uploads/kept.jpg');
    });

    it('consults `saveFileAttachmentFromAnotherRecord()` for unknown `data-id`s that do not resolve to an existing file', function (): void {
        $editor = (new RichEditor('content'))
            ->container(Schema::make(Livewire::make())->statePath('data'));

        $editor->getFileAttachmentUrlUsing(static function (string $file): ?string {
            return str_starts_with($file, 'migrated/') ? "https://cdn.test/{$file}" : null;
        });

        $editor->saveFileAttachmentFromAnotherRecordUsing(
            static fn (string $file): string => "migrated/{$file}",
        );

        $editor->rawState('<p><img src="placeholder" data-id="foreign-id" /></p>');

        $editor->resolveFileAttachmentIds();

        $srcs = [];

        walkEditorRawState($editor, function (array $node) use (&$srcs): void {
            if (($node['type'] ?? null) === 'image') {
                $srcs[$node['attrs']['id'] ?? ''] = $node['attrs']['src'] ?? null;
            }
        });

        expect($srcs)->toHaveKey('migrated/foreign-id');
        expect($srcs['migrated/foreign-id'])->toBe('https://cdn.test/migrated/foreign-id');
    });
});

function walkEditorRawState(RichEditor $editor, callable $callback): void
{
    $decoded = json_decode(json_encode($editor->getRawState()), true);

    $walker = function ($nodes) use (&$walker, $callback): void {
        foreach ($nodes as $node) {
            $callback($node);

            if (! empty($node['content'] ?? null)) {
                $walker($node['content']);
            }
        }
    };

    $walker($decoded['content'] ?? []);
}

it('exposes TipTap and ProseMirror modules on `window.FilamentRichEditor.tiptap` once the editor has mounted', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/rich-editor-browser-test')
            ->assertPresent('.fi-fo-rich-editor .tiptap')
            ->assertScript("typeof window.FilamentRichEditor?.tiptap?.core?.Editor === 'function'")
            ->assertScript("typeof window.FilamentRichEditor?.tiptap?.pmState?.Plugin === 'function'")
            ->assertScript("typeof window.FilamentRichEditor?.tiptap?.pmView?.EditorView === 'function'")
            ->assertScript("typeof window.FilamentRichEditor?.tiptap?.pmModel?.Node === 'function'");
    });
});

class RenderRichEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([RichEditor::make('content')])->statePath('data');
    }
}

class RenderRichEditorWithToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->toolbarButtons([['bold', 'italic'], ['undo', 'redo']]),
        ])->statePath('data');
    }
}

class RenderRichEditorWithClosureToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->toolbarButtons(static fn (): array => [['bold', 'italic']]),
        ])->statePath('data');
    }
}

class RenderRichEditorWithDisabledToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->disableToolbarButtons(['bold', 'italic', 'attachFiles']),
        ])->statePath('data');
    }
}

class RenderRichEditorWithEnabledToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->enableToolbarButtons(['h1', 'textColor']),
        ])->statePath('data');
    }
}

class RenderRichEditorWithNoToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->disableAllToolbarButtons(),
        ])->statePath('data');
    }
}

class RenderRichEditorWithAllToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->disableAllToolbarButtons(false),
        ])->statePath('data');
    }
}

class RenderRichEditorWithMergeTags extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->mergeTags(['{{name}}', '{{email}}']),
        ])->statePath('data');
    }
}

class RenderRichEditorWithClosureMergeTags extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->mergeTags(static fn (): array => ['{{name}}', '{{email}}']),
        ])->statePath('data');
    }
}

class RenderRichEditorWithLinkProtocols extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->linkProtocols(['https', 'mailto']),
        ])->statePath('data');
    }
}

class RenderRichEditorWithClosureLinkProtocols extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->linkProtocols(static fn (): array => ['https', 'tel']),
        ])->statePath('data');
    }
}

class RenderRichEditorWithTextColors extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->textColors(['red' => '#ff0000', 'blue' => '#0000ff']),
        ])->statePath('data');
    }
}

class RenderRichEditorWithResizableImages extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->resizableImages(),
        ])->statePath('data');
    }
}

class RenderRichEditorWithClosureResizableImages extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->resizableImages(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderRichEditorWithActivePanel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->activePanel('merge-tags'),
        ])->statePath('data');
    }
}

class RenderRichEditorWithClosureActivePanel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->activePanel(static fn (): string => 'custom-panel'),
        ])->statePath('data');
    }
}

class RenderRichEditorWithNullActivePanel extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->activePanel('merge-tags')->activePanel(null),
        ])->statePath('data');
    }
}

class RenderRichEditorWithUploadingFileMessage extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->uploadingFileMessage('Uploading file...'),
        ])->statePath('data');
    }
}

class RenderRichEditorWithClosureUploadingFileMessage extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->uploadingFileMessage(static fn (): string => 'Uploading...'),
        ])->statePath('data');
    }
}

class RenderRichEditorWithJson extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([RichEditor::make('content')->json()])->statePath('data');
    }
}

class RenderRichEditorWithClosureJson extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->json(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderRichEditorWithJsonFalse extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->json()->json(false),
        ])->statePath('data');
    }
}

class RenderRichEditorWithCustomTextColors extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->customTextColors(),
        ])->statePath('data');
    }
}

class RenderRichEditorWithClosureCustomTextColors extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->customTextColors(static fn (): bool => true),
        ])->statePath('data');
    }
}

class RenderRichEditorWithCustomTextColorsFalse extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->customTextColors()->customTextColors(false),
        ])->statePath('data');
    }
}

class RenderRichEditorWithClosureCustomBlocks extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->customBlocks(static fn (): array => []),
        ])->statePath('data');
    }
}

class RenderRichEditorWithNoMergeTagSearchResultsMessage extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->noMergeTagSearchResultsMessage('No tags found'),
        ])->statePath('data');
    }
}

class RenderRichEditorWithNoFileAttachments extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')->fileAttachments(false),
        ])->statePath('data');
    }
}

class RenderRichEditorWithPluginEnabledButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')
                ->plugins([new TestRichContentPlugin(enabledButtons: ['highlight'])]),
        ])->statePath('data');
    }
}

class RenderRichEditorWithPluginDisabledButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            RichEditor::make('content')
                ->plugins([new TestRichContentPlugin(disabledButtons: ['bold', 'italic'])]),
        ])->statePath('data');
    }
}

// Custom block test fixtures

class RichEditorTestBlockA extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'block-a';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div>A</div>';
    }
}

class RichEditorTestBlockB extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'block-b';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div>B</div>';
    }
}

class RichEditorTestBlockC extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'block-c';
    }

    public static function toHtml(array $config, array $data): ?string
    {
        return '<div>C</div>';
    }
}

class TestComponentWithRichEditorRecord extends Livewire
{
    public Post $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                RichEditor::make('content')
                    ->fileAttachmentsDisk('local'),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}

class TestComponentWithRichEditorRecordPreventingTampering extends Livewire
{
    public Post $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                RichEditor::make('content')
                    ->fileAttachmentsDisk('local')
                    ->preventFileAttachmentPathTampering(),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}

class TestComponentWithRichEditorRecordAllowingTemplatePaths extends Livewire
{
    public Post $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                RichEditor::make('content')
                    ->fileAttachmentsDisk('local')
                    ->preventFileAttachmentPathTampering(
                        allowFilePathUsing: static fn (string $file): bool => str_starts_with($file, 'templates/'),
                    ),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}

class TestComponentWithRichEditorPreventingTamperingWithoutRecord extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                RichEditor::make('content')
                    ->fileAttachmentsDisk('local')
                    ->preventFileAttachmentPathTampering(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->form->getState();
    }
}

class TestComponentWithRichEditorRecordPreventingTamperingAndCustomMessage extends Livewire
{
    public Post $record;

    public function mount(): void
    {
        $this->form->fill($this->record->attributesToArray());
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                RichEditor::make('content')
                    ->fileAttachmentsDisk('local')
                    ->preventFileAttachmentPathTampering()
                    ->validationMessages([
                        'tampered' => 'The content references an image that is not permitted.',
                    ]),
            ])
            ->model($this->record)
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->form->getState());
    }
}
