<?php

use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Livewire\Topbar;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('allows uploads only to an action mounted on that topbar', function (string $fieldClass): void {
    $this->actingAs(User::factory()->create());
    Filament::setCurrentPanel('admin');

    Filament::getCurrentOrDefaultPanel()->userMenuItems([
        Action::make('upload')->schema([$fieldClass::make('photo')]),
    ]);

    $statePath = 'mountedActions.0.data.photo';

    if ($fieldClass === MarkdownEditor::class) {
        $statePath = "componentFileAttachments.{$statePath}";
    }

    livewire(Topbar::class)
        ->call('mountAction', 'upload')
        ->call('_startUpload', $statePath, [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertDispatched('upload:generatedSignedUrl');

    livewire(Topbar::class)
        ->call('_startUpload', $statePath, [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
})->with([
    'file upload' => [FileUpload::class],
    'attachment editor' => [MarkdownEditor::class],
]);

it('rejects temporary uploads without a file-upload field in its own schema', function (): void {
    $this->actingAs(User::factory()->create());

    livewire(Topbar::class)
        ->call('_startUpload', 'componentFileAttachments.data.photo', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});
