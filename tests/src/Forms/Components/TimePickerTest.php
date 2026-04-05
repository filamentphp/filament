<?php

namespace Filament\Tests\Forms\Components;

use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Schema;
use Filament\Tests\Fixtures\Livewire\Livewire;
use Filament\Tests\Fixtures\Models\User;
use Filament\Tests\TestCase;
use Illuminate\Support\Facades\Artisan;

use function Filament\Tests\livewire;

uses(TestCase::class);

beforeEach(function (): void {
    Artisan::call('filament:assets');
});

it('can render', function (): void {
    livewire(TestComponentWithTimePicker::class)
        ->assertSuccessful();
});

it('can set and get state', function (): void {
    livewire(TestComponentWithTimePicker::class)
        ->fillForm(['time' => '14:30:00'])
        ->assertSchemaStateSet(['time' => '14:30:00']);
});

it('can render without seconds', function (): void {
    livewire(TestComponentWithTimePickerNoSeconds::class)
        ->assertSuccessful();
});

describe('`hasDate()` override effects', function (): void {
    it('returns `false` for `hasDate()`', function (): void {
        $picker = TimePicker::make('time');

        expect($picker->hasDate())->toBeFalse();
    });

    it('returns `true` for `hasTime()`', function (): void {
        $picker = TimePicker::make('time');

        expect($picker->hasTime())->toBeTrue();
    });

    it('returns `time` from `getType()`', function (): void {
        $picker = TimePicker::make('time');

        expect($picker->getType())->toBe('time');
    });

    it('returns time-with-seconds format from `getFormat()` by default', function (): void {
        $picker = TimePicker::make('time');

        expect($picker->getFormat())->toBe('H:i:s');
    });

    it('returns time-without-seconds format from `getFormat()` when seconds disabled', function (): void {
        $picker = TimePicker::make('time')->seconds(false);

        expect($picker->getFormat())->toBe('H:i');
    });

    it('returns time-with-seconds display format from `getDisplayFormat()` by default', function (): void {
        $picker = TimePicker::make('time');

        expect($picker->getDisplayFormat())->toBe('H:i:s');
    });

    it('returns time-without-seconds display format from `getDisplayFormat()` when seconds disabled', function (): void {
        $picker = TimePicker::make('time')->seconds(false);

        expect($picker->getDisplayFormat())->toBe('H:i');
    });

    it('returns `H:i:s` from `getInternalFormat()` when native', function (): void {
        $picker = TimePicker::make('time')->native();

        expect($picker->getInternalFormat())->toBe('H:i:s');
    });

    it('returns `H:i` from `getInternalFormat()` when native and seconds disabled', function (): void {
        $picker = TimePicker::make('time')->native()->seconds(false);

        expect($picker->getInternalFormat())->toBe('H:i');
    });

    it('returns `1` from `getStep()` by default (has seconds)', function (): void {
        $picker = TimePicker::make('time');

        expect($picker->getStep())->toBe(1);
    });

    it('returns `null` from `getStep()` when seconds disabled and no custom steps', function (): void {
        $picker = TimePicker::make('time')->seconds(false);

        expect($picker->getStep())->toBeNull();
    });
});

it('has no accessibility issues in light mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/time-picker-browser-test')
            ->assertSee('Test Time Picker')
            ->assertNoSmoke()
            ->assertNoAccessibilityIssues();
    });
});

it('has no accessibility issues in dark mode', function (): void {
    retry(10, function (): void {
        $this->actingAs(User::factory()->create());

        visit('/time-picker-browser-test')
            ->inDarkMode()
            ->assertNoAccessibilityIssues();
    });
});

class TestComponentWithTimePicker extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TimePicker::make('time'),
            ])
            ->statePath('data');
    }
}

class TestComponentWithTimePickerNoSeconds extends Livewire
{
    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                TimePicker::make('time')->seconds(false),
            ])
            ->statePath('data');
    }
}
