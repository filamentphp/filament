<?php

declare(strict_types=1);

namespace Filament\Forms\Tests;

use Filament\Forms\Components\TextInput;

class PasswordToggleAccessibilityTest extends TestCase
{
    /** @test */
    public function password_toggle_renders_with_accessibility_attributes(): void
    {
        $component = TextInput::make('secret')->password();

        $html = (string) $this->renderFormComponent($component);

        $this->assertStringContainsString('type="button"', $html);
        $this->assertStringContainsString('aria-pressed', $html);
        $this->assertStringContainsString('aria-label="Show password"', $html);
        $this->assertStringContainsString('sr-only', $html);
    }
}
?>