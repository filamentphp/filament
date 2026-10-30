<?php

namespace Filament\Tests\Upgrade;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Icons\Heroicon;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\TestCase;
use Filament\Tests\Upgrade\BehavioralRuntimeFixtures\BehavioralConfigurationManual as Configuration;
use Filament\Tests\Upgrade\BehavioralRuntimeFixtures\BehavioralExpressions;

class BehavioralConfigurationMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists(BehavioralExpressions::class . '\\components')) {
            foreach (['behavioral_configuration_expressions', 'behavioral_configuration_manual'] as $fixture) {
                [, $migrated] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/' . $fixture . '.php.inc'), 2);
                eval('?>' . str_replace('namespace Filament\\Tests\\Upgrade\\Fixtures\\', 'namespace Filament\\Tests\\Upgrade\\BehavioralRuntimeFixtures\\', $migrated));
            }
        }
    }

    public function test_evaluates_and_negates_injected_closures_without_losing_boolean_snapshots(): void
    {
        $events = [];
        $condition = true;
        $components = BehavioralExpressions\components($events, $condition);
        Schema::make(Livewire::make())->components(array_filter($components, static fn ($component): bool => $component instanceof Field))->getComponents();

        $this->assertSame(['eager'], $events);
        $this->assertFalse($components[0]->isPreviewable());
        $condition = false;
        $this->assertTrue($components[0]->isPreviewable());
        $this->assertFalse($components[1]->isPreviewable());
        $this->assertSame('16:9', $components[2]->getImageAspectRatio());
        $this->assertTrue($components[2]->shouldAutomaticallyCropImagesToAspectRatio());
        $this->assertSame(['eager', 'preview', 'preview', 'ratio', 'ratio'], $events);
        $this->assertSame('off', $components[3]->getAutocomplete());
        $this->assertFalse($components[4]->isAddable());
        $this->assertFalse($components[5]->canSelectPlaceholder());
        $this->assertSame(Alignment::End, $components[6]->getModalAlignment());
        $this->assertFalse($components[7]->canCreateAnother());
        $this->assertSame(Heroicon::Clock, $components[8]->getSuffixIcon());
        $this->assertTrue($components[8]->isSuffixInline());
        BehavioralExpressions\configureTyped($components[0], $components[6]);
        $this->assertTrue($components[0]->isPreviewable());
        $this->assertSame(Alignment::Center, $components[6]->getModalAlignment());
    }

    public function test_migrates_typed_and_nullable_configuration(): void
    {
        $this->assertNull(Configuration\configure(null, true));

        foreach ([true, false] as $condition) {
            foreach ([$condition, static fn (): bool => $condition] as $configuration) {
                $upload = FileUpload::make('photo');
                $this->assertSame($upload, Configuration\configure($upload, $configuration));
                $this->assertSame(! $condition, $upload->isPreviewable());

                $input = Configuration\autocomplete(TextInput::make('name'), $configuration);
                $this->assertSame($condition ? 'off' : null, $input->getAutocomplete());
                $this->assertSame($condition ? 'off' : null, $input->getAutocapitalize());

                $action = Action::make('edit')->modalAlignment(Alignment::End);
                $this->assertSame($action, Configuration\center($action, $configuration));
                $this->assertSame($condition ? Alignment::Center : Alignment::End, $action->getModalAlignment());
            }
        }

        foreach ([['16:9', '16:9', true], ['', null, false], [null, null, false], [static fn (): string => '4:3', '4:3', true]] as [$ratio, $expectedRatio, $shouldCrop]) {
            $upload = Configuration\ratio(FileUpload::make('photo'), $ratio);
            $this->assertSame($expectedRatio, $upload->getImageAspectRatio());
            $this->assertSame($shouldCrop, $upload->shouldAutomaticallyCropImagesToAspectRatio());
        }

        foreach (['heroicon-o-clock', false, null] as $icon) {
            $picker = DateTimePicker::make('date')->suffixIcon('heroicon-o-star', isInline: false);
            $this->assertSame($picker, Configuration\icon($picker, $icon));
            $this->assertSame($icon === false ? 'heroicon-o-star' : ($icon ?? Heroicon::Calendar), $picker->getSuffixIcon());
            $this->assertSame($icon !== false, $picker->isSuffixInline());
        }

        $action = Action::make('edit')->modalAlignment(Alignment::End);
        $this->assertSame($action, Configuration\center($action, null));
        $this->assertSame(Alignment::End, $action->getModalAlignment());
    }
}
