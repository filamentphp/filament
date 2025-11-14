<?php

use Filament\Forms\Components\MarkdownEditor;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Illuminate\Validation\ValidationException;

uses(TestCase::class);

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

test('`hasFileAttachments()` returns true by default', function (): void {
    $markdownEditor = Schema::make(Livewire::make())
        ->statePath('data')
        ->components([
            MarkdownEditor::make('content'),
        ])
        ->getComponents()[0];

    expect($markdownEditor->hasFileAttachments())->toBeTrue()
        ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeTrue();
});

test('`hasFileAttachments()` returns false when `attachFiles` button is removed using `disableToolbarButtons()`', function (): void {
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

test('`hasFileAttachments()` returns true when `attachFiles` is in custom toolbar buttons', function (): void {
    $markdownEditor = MarkdownEditor::make('content')
        ->toolbarButtons([
            ['bold', 'italic'],
            ['attachFiles'],
        ]);

    expect($markdownEditor->hasFileAttachments())->toBeTrue()
        ->and($markdownEditor->hasToolbarButton('attachFiles'))->toBeTrue();
});

test('`hasFileAttachments()` returns false with custom toolbar buttons without `attachFiles`', function (): void {
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
