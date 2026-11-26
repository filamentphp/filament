<?php

use Filament\Livewire\Sidebar;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;

use function Filament\Tests\livewire;

uses(TestCase::class);

it('rejects temporary uploads without a file-upload field in its own schema', function (): void {
    $this->actingAs(User::factory()->create());

    livewire(Sidebar::class)
        ->call('_startUpload', 'componentFileAttachments.data.photo', [['name' => 'a.jpg', 'size' => 1024, 'type' => 'image/jpeg']], false)
        ->assertForbidden();
});
