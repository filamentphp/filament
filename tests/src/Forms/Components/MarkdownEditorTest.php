<?php

use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Validation\ValidationException;

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
                $field = (new MarkdownEditor('content'))
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
        $markdownEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ])
            ->getComponents()[0];

        $defaultButtons = $markdownEditor->getDefaultToolbarButtons();

        expect($defaultButtons)
            ->toBeArray()
            ->toHaveCount(5)
            ->and($defaultButtons[0])->toEqual(['bold', 'italic', 'strike', 'link'])
            ->and($defaultButtons[1])->toEqual(['heading'])
            ->and($defaultButtons[2])->toEqual(['blockquote', 'codeBlock', 'bulletList', 'orderedList'])
            ->and($defaultButtons[3])->toEqual(['table', 'attachFiles'])
            ->and($defaultButtons[4])->toEqual(['undo', 'redo']);
    });

    test('can overwrite toolbar buttons array using `toolbarButtons()`', function (): void {
        $markdownEditor = MarkdownEditor::make('content')
            ->toolbarButtons([
                ['bold', 'italic'],
                ['undo', 'redo'],
            ]);

        $buttons = $markdownEditor->getToolbarButtons();

        expect($buttons)
            ->toBeArray()
            ->toHaveCount(2)
            ->and($buttons[0])->toEqual(['bold', 'italic'])
            ->and($buttons[1])->toEqual(['undo', 'redo']);
    });

    test('can overwrite toolbar buttons with closure using `toolbarButtons()`', function (): void {
        $markdownEditor = MarkdownEditor::make('content')
            ->toolbarButtons(fn () => [
                ['bold', 'italic'],
            ]);

        $buttons = $markdownEditor->getToolbarButtons();

        expect($buttons)
            ->toBeArray()
            ->toHaveCount(1)
            ->and($buttons[0])->toEqual(['bold', 'italic']);
    });

    test('can disable specific toolbar buttons using `disableToolbarButtons()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ]);

        $markdownEditor = $schema->getComponents()[0];
        $markdownEditor->disableToolbarButtons(['bold', 'italic', 'attachFiles']);

        $buttons = $markdownEditor->getToolbarButtons();

        // Check that `bold`, `italic`, and `attachFiles` buttons are not present
        $flatButtons = array_merge(...$buttons);

        expect($flatButtons)
            ->not->toContain('bold')
            ->not->toContain('italic')
            ->not->toContain('attachFiles')
            ->toContain('strike')
            ->toContain('link')
            ->toContain('undo');
    });

    test('can enable additional toolbar buttons using `enableToolbarButtons()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ]);

        $markdownEditor = $schema->getComponents()[0];
        $markdownEditor->enableToolbarButtons(['underline', 'subscript']);

        $buttons = $markdownEditor->getToolbarButtons();

        // Check that all default buttons plus `underline` and `subscript` are present
        $flatButtons = array_merge(...$buttons);

        expect($flatButtons)
            ->toContain('bold')
            ->toContain('italic')
            ->toContain('underline')
            ->toContain('subscript');
    });

    test('can disable all toolbar buttons using `disableAllToolbarButtons()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ]);

        $markdownEditor = $schema->getComponents()[0];
        $markdownEditor->disableAllToolbarButtons();

        $buttons = $markdownEditor->getToolbarButtons();

        expect($buttons)->toBeArray()->toBeEmpty();
    });

    test('can conditionally disable all toolbar buttons using `disableAllToolbarButtons()`', function (): void {
        $markdownEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content')
                    ->disableAllToolbarButtons(false),
            ])
            ->getComponents()[0];

        $buttons = $markdownEditor->getToolbarButtons();

        expect($buttons)->toBeArray()->not->toBeEmpty();
    });

    test('can check if toolbar button exists using `hasToolbarButton()`', function (): void {
        $markdownEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ])
            ->getComponents()[0];

        expect($markdownEditor->hasToolbarButton('bold'))->toBeTrue()
            ->and($markdownEditor->hasToolbarButton('italic'))->toBeTrue()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeTrue()
            ->and($markdownEditor->hasToolbarButton('nonexistent'))->toBeFalse();
    });

    test('can check if toolbar button exists with array using `hasToolbarButton()`', function (): void {
        $markdownEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ])
            ->getComponents()[0];

        expect($markdownEditor->hasToolbarButton(['bold', 'italic']))->toBeTrue()
            ->and($markdownEditor->hasToolbarButton(['nonexistent1', 'nonexistent2']))->toBeFalse()
            ->and($markdownEditor->hasToolbarButton(['bold', 'nonexistent']))->toBeTrue(); // At least one exists
    });

    test('can check if custom toolbar buttons are set using `hasCustomToolbarButtons()`', function (): void {
        $markdownEditor = MarkdownEditor::make('content');

        expect($markdownEditor->hasCustomToolbarButtons())->toBeFalse();

        $markdownEditor->toolbarButtons([['bold', 'italic']]);

        expect($markdownEditor->hasCustomToolbarButtons())->toBeTrue();
    });

    test('toolbar buttons are properly grouped by `getToolbarButtons()`', function (): void {
        $markdownEditor = MarkdownEditor::make('content')
            ->toolbarButtons([
                ['bold', 'italic'],
                'strike',
                'link',
            ]);

        $buttons = $markdownEditor->getToolbarButtons();

        // The `getToolbarButtons()` method groups consecutive non-array buttons together.
        // When an array is encountered, it becomes its own group, and any preceding
        // non-array buttons are grouped into their own group at the end.
        expect($buttons)
            ->toBeArray()
            ->toHaveCount(2)
            ->and($buttons[0])->toEqual(['bold', 'italic'])
            ->and($buttons[1])->toEqual(['strike', 'link']);
    });

    test('blank button groups are filtered out by `getToolbarButtons()`', function (): void {
        $markdownEditor = MarkdownEditor::make('content')
            ->toolbarButtons([
                ['bold', 'italic'],
                [],
                ['undo', 'redo'],
            ]);

        $buttons = $markdownEditor->getToolbarButtons();

        expect($buttons)
            ->toBeArray()
            ->toHaveCount(2)
            ->and($buttons[0])->toEqual(['bold', 'italic'])
            ->and($buttons[1])->toEqual(['undo', 'redo']);
    });

    test('cannot use `disableToolbarButtons()` when using closure', function (): void {
        $markdownEditor = MarkdownEditor::make('content')
            ->toolbarButtons(fn () => [['bold', 'italic']]);

        expect(fn () => $markdownEditor->disableToolbarButtons(['bold']))
            ->toThrow(LogicException::class, 'You cannot use the `disableToolbarButtons()` method when the toolbar buttons are dynamically returned from a function.');
    });

    test('cannot use `enableToolbarButtons()` when using closure', function (): void {
        $markdownEditor = MarkdownEditor::make('content')
            ->toolbarButtons(fn () => [['bold', 'italic']]);

        expect(fn () => $markdownEditor->enableToolbarButtons(['strike']))
            ->toThrow(LogicException::class, 'You cannot use the `enableToolbarButtons()` method when the toolbar buttons are dynamically returned from a function.');
    });

});

describe('file attachments', function (): void {
    test('`hasFileAttachments()` returns `true` by default', function (): void {
        $markdownEditor = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ])
            ->getComponents()[0];

        expect($markdownEditor->hasFileAttachments())->toBeTrue()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeTrue();
    });

    test('`hasFileAttachments()` returns `false` when `attachFiles` button is removed using `disableToolbarButtons()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ]);

        $markdownEditor = $schema->getComponents()[0];
        $markdownEditor->disableToolbarButtons(['attachFiles']);

        expect($markdownEditor->hasFileAttachments())->toBeFalse()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeFalse();
    });

    test('`hasFileAttachments()` returns `true` when `attachFiles` is in custom toolbar buttons', function (): void {
        $markdownEditor = MarkdownEditor::make('content')
            ->toolbarButtons([
                ['bold', 'italic'],
                ['attachFiles'],
            ]);

        expect($markdownEditor->hasFileAttachments())->toBeTrue()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeTrue();
    });

    test('`hasFileAttachments()` returns `false` with custom toolbar buttons without `attachFiles`', function (): void {
        $markdownEditor = MarkdownEditor::make('content')
            ->toolbarButtons([
                ['bold', 'italic'],
                ['undo', 'redo'],
            ]);

        expect($markdownEditor->hasFileAttachments())->toBeFalse()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeFalse();
    });

    test('`fileAttachments()` method takes precedence over `disableToolbarButtons()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ]);

        $markdownEditor = $schema->getComponents()[0];
        $markdownEditor->disableToolbarButtons(['bold']);
        $markdownEditor->fileAttachments(false);

        expect($markdownEditor->hasFileAttachments())->toBeFalse()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeFalse();

        $buttons = $markdownEditor->getToolbarButtons();
        $flatButtons = array_merge(...$buttons);

        expect($flatButtons)->not->toContain('attachFiles');
    });

    test('`fileAttachments(false)` works when called before `disableToolbarButtons()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ]);

        $markdownEditor = $schema->getComponents()[0];
        $markdownEditor->fileAttachments(false);
        $markdownEditor->disableToolbarButtons(['bold']);

        expect($markdownEditor->hasFileAttachments())->toBeFalse()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeFalse();

        $buttons = $markdownEditor->getToolbarButtons();
        $flatButtons = array_merge(...$buttons);

        expect($flatButtons)->not->toContain('attachFiles');
    });

    test('`disableToolbarButtons()` with `attachFiles` also makes `hasFileAttachments()` return false', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ]);

        $markdownEditor = $schema->getComponents()[0];
        $markdownEditor->disableToolbarButtons(['attachFiles']);

        expect($markdownEditor->hasFileAttachments())->toBeFalse()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeFalse();
    });

    test('`fileAttachments(true)` does not force `attachFiles` button to appear when using `disableToolbarButtons()`', function (): void {
        $schema = Schema::make(Livewire::make())
            ->statePath('data')
            ->components([
                MarkdownEditor::make('content'),
            ]);

        $markdownEditor = $schema->getComponents()[0];
        $markdownEditor->disableToolbarButtons(['attachFiles']);
        $markdownEditor->fileAttachments(true);

        // File attachments are enabled (drag/drop works), but the toolbar button remains hidden
        expect($markdownEditor->hasFileAttachments())->toBeTrue()
            ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeFalse();
    });
});

describe('file attachment disk', function (): void {
    it('returns `public` for `getFileAttachmentsDiskName()` when config default is `local`', function (): void {
        Config::set('filament.default_filesystem_disk', 'local');

        $editor = MarkdownEditor::make('content');

        expect($editor->getFileAttachmentsDiskName())->toBe('public');
    });

    it('returns config default for `getFileAttachmentsDiskName()` when not `local`', function (): void {
        Config::set('filament.default_filesystem_disk', 's3');

        $editor = MarkdownEditor::make('content');

        expect($editor->getFileAttachmentsDiskName())->toBe('s3');
    });

    it('prioritizes explicit disk over config default', function (): void {
        Config::set('filament.default_filesystem_disk', 'local');

        $editor = MarkdownEditor::make('content')
            ->fileAttachmentsDisk('custom-disk');

        expect($editor->getFileAttachmentsDiskName())->toBe('custom-disk');
    });
});

describe('file attachment visibility', function (): void {
    it('always returns `public` for `getFileAttachmentsVisibility()`', function (): void {
        $editor = MarkdownEditor::make('content');

        expect($editor->getFileAttachmentsVisibility())->toBe('public');
    });

    it('throws `LogicException` when calling `fileAttachmentsVisibility()`', function (): void {
        $editor = MarkdownEditor::make('content');

        $editor->fileAttachmentsVisibility('private');
    })->throws(LogicException::class);
});

describe('height constraints', function (): void {
    it('defaults `getMinHeight()` to `11.25rem`', function (): void {
        $editor = MarkdownEditor::make('content');

        expect($editor->getMinHeight())->toBe('11.25rem');
    });

    it('can set `minHeight()`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->minHeight('20rem');

        expect($editor->getMinHeight())->toBe('20rem');
    });

    it('can set `minHeight()` with a `Closure`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->minHeight(static fn (): string => '15rem');

        expect($editor->getMinHeight())->toBe('15rem');
    });

    it('can clear `minHeight()` with `null`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->minHeight(null);

        expect($editor->getMinHeight())->toBeNull();
    });

    it('returns `null` for `getMaxHeight()` by default', function (): void {
        $editor = MarkdownEditor::make('content');

        expect($editor->getMaxHeight())->toBeNull();
    });

    it('can set `maxHeight()`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->maxHeight('40rem');

        expect($editor->getMaxHeight())->toBe('40rem');
    });

    it('can set `maxHeight()` with a `Closure`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->maxHeight(static fn (): string => '50rem');

        expect($editor->getMaxHeight())->toBe('50rem');
    });
});

describe('length constraints', function (): void {
    it('returns `null` for `getMaxLength()` by default', function (): void {
        $editor = MarkdownEditor::make('content');

        expect($editor->getMaxLength())->toBeNull();
    });

    it('can set `maxLength()`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->maxLength(1000);

        expect($editor->getMaxLength())->toBe(1000);
    });

    it('can set `maxLength()` with a `Closure`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->maxLength(static fn (): int => 500);

        expect($editor->getMaxLength())->toBe(500);
    });

    it('returns `null` for `getMinLength()` by default', function (): void {
        $editor = MarkdownEditor::make('content');

        expect($editor->getMinLength())->toBeNull();
    });

    it('can set `minLength()`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->minLength(10);

        expect($editor->getMinLength())->toBe(10);
    });

    it('can set `minLength()` with a `Closure`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->minLength(static fn (): int => 5);

        expect($editor->getMinLength())->toBe(5);
    });

    it('can set `length()` which sets both min and max', function (): void {
        $editor = MarkdownEditor::make('content')
            ->length(100);

        expect($editor->getLength())->toBe(100);
        expect($editor->getMinLength())->toBe(100);
        expect($editor->getMaxLength())->toBe(100);
    });

    it('generates `max` rule from `maxLength()`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->maxLength(1000);

        $rules = $editor->getLengthValidationRules();

        expect($rules)->toContain('max:1000');
    });

    it('generates `min` rule from `minLength()`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->minLength(10);

        $rules = $editor->getLengthValidationRules();

        expect($rules)->toContain('min:10');
    });

    it('generates `size` rule from `length()`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->length(50);

        $rules = $editor->getLengthValidationRules();

        expect($rules)->toContain('size:50');
    });
});

describe('placeholder', function (): void {
    it('returns `null` for `getPlaceholder()` by default', function (): void {
        $editor = MarkdownEditor::make('content');

        expect($editor->getPlaceholder())->toBeNull();
    });

    it('can set `placeholder()`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->placeholder('Write your content...');

        expect($editor->getPlaceholder())->toBe('Write your content...');
    });

    it('can set `placeholder()` with a `Closure`', function (): void {
        $editor = MarkdownEditor::make('content')
            ->placeholder(static fn (): string => 'Dynamic placeholder');

        expect($editor->getPlaceholder())->toBe('Dynamic placeholder');
    });
});

describe('rendering', function (): void {
    it('can render', function (): void {
        livewire(RenderMarkdownEditor::class)
            ->assertSuccessful();
    });

    it('can render with custom `toolbarButtons()`', function (): void {
        livewire(RenderMarkdownEditorWithToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `toolbarButtons()` set via `Closure`', function (): void {
        livewire(RenderMarkdownEditorWithClosureToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `disableToolbarButtons()`', function (): void {
        livewire(RenderMarkdownEditorWithDisabledToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `enableToolbarButtons()`', function (): void {
        livewire(RenderMarkdownEditorWithEnabledToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `disableAllToolbarButtons()`', function (): void {
        livewire(RenderMarkdownEditorWithNoToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `disableAllToolbarButtons(false)`', function (): void {
        livewire(RenderMarkdownEditorWithAllToolbarButtons::class)
            ->assertSuccessful();
    });

    it('can render with `maxHeight()`', function (): void {
        livewire(RenderMarkdownEditorWithMaxHeight::class)
            ->assertSuccessful();
    });

    it('can render with `maxHeight()` set via `Closure`', function (): void {
        livewire(RenderMarkdownEditorWithClosureMaxHeight::class)
            ->assertSuccessful();
    });

    it('can render with `minHeight()`', function (): void {
        livewire(RenderMarkdownEditorWithMinHeight::class)
            ->assertSuccessful();
    });

    it('can render with `minHeight()` set via `Closure`', function (): void {
        livewire(RenderMarkdownEditorWithClosureMinHeight::class)
            ->assertSuccessful();
    });

    it('can render with `minHeight(null)`', function (): void {
        livewire(RenderMarkdownEditorWithNullMinHeight::class)
            ->assertSuccessful();
    });

    it('can render with `placeholder()`', function (): void {
        livewire(RenderMarkdownEditorWithPlaceholder::class)
            ->assertSuccessful();
    });

    it('can render with `placeholder()` set via `Closure`', function (): void {
        livewire(RenderMarkdownEditorWithClosurePlaceholder::class)
            ->assertSuccessful();
    });

    it('can render with `fileAttachments(false)`', function (): void {
        livewire(RenderMarkdownEditorWithNoFileAttachments::class)
            ->assertSuccessful();
    });
});

it('can render `MarkdownEditor` in the browser', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/markdown-editor-browser-test')
            ->assertSee('Content')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();

        visit('/markdown-editor-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class RenderMarkdownEditor extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([MarkdownEditor::make('content')])->statePath('data');
    }
}

class RenderMarkdownEditorWithToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')
                ->toolbarButtons([['bold', 'italic'], ['undo', 'redo']]),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithClosureToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')
                ->toolbarButtons(static fn (): array => [['bold', 'italic']]),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithDisabledToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')
                ->disableToolbarButtons(['bold', 'italic', 'attachFiles']),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithEnabledToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')
                ->enableToolbarButtons(['underline', 'subscript']),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithNoToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')
                ->disableAllToolbarButtons(),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithAllToolbarButtons extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')
                ->disableAllToolbarButtons(false),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithMaxHeight extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([MarkdownEditor::make('content')->maxHeight('40rem')])->statePath('data');
    }
}

class RenderMarkdownEditorWithClosureMaxHeight extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')->maxHeight(static fn (): string => '50rem'),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithMinHeight extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([MarkdownEditor::make('content')->minHeight('20rem')])->statePath('data');
    }
}

class RenderMarkdownEditorWithClosureMinHeight extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')->minHeight(static fn (): string => '15rem'),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithNullMinHeight extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([MarkdownEditor::make('content')->minHeight(null)])->statePath('data');
    }
}

class RenderMarkdownEditorWithPlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')->placeholder('Write your content...'),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithClosurePlaceholder extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([
            MarkdownEditor::make('content')->placeholder(static fn (): string => 'Dynamic placeholder'),
        ])->statePath('data');
    }
}

class RenderMarkdownEditorWithNoFileAttachments extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form->schema([MarkdownEditor::make('content')->fileAttachments(false)])->statePath('data');
    }
}
