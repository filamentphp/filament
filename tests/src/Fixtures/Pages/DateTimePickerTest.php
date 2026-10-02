<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class DateTimePickerTest extends Page
{
    protected string $view = 'pages.date-time-picker-test';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?int $navigationSort = 9;

    public ?array $data = [];

    public bool $hasDate = true;

    public bool $hasSeconds = true;

    public bool $hasLimits = false;

    public bool $isNative = true;

    public bool $hasDstLimits = false;

    public bool $hasAppDstGap = false;

    public array $saved = [];

    public int $saveCount = 0;

    public int $reloadCount = 0;

    public function mount(): void
    {
        $this->hasDate = request()->boolean('date', true);
        $this->hasSeconds = request()->boolean('seconds', true);
        $this->hasLimits = request()->boolean('bounds');
        $this->isNative = request()->boolean('native', true);
        $this->hasDstLimits = request()->boolean('dst');
        $this->hasAppDstGap = request()->boolean('app-dst-gap');
        $this->form->fill($this->hasDstLimits ? ['field' => '01:45:07'] : []);
    }

    public function form(Schema $form): Schema
    {
        $field = ($this->hasDate ? DateTimePicker::make('field') : TimePicker::make('field'))
            ->label('Test DateTimePicker')
            ->native($this->isNative)
            ->seconds($this->hasSeconds)
            ->extraAttributes(['data-testid' => 'date-time-picker'])
            ->extraInputAttributes(['data-testid' => 'timed-input'])
            ->extraTriggerAttributes(['data-testid' => 'timed-trigger']);

        if ($this->hasLimits) {
            $field->timezone('Asia/Kathmandu')
                ->minDate(Carbon::parse('2025-07-15 00:30:23', 'Asia/Tokyo'))
                ->maxDate(static fn () => CarbonImmutable::parse('2025-07-17 17:45:47', 'America/Los_Angeles'));
        }

        if ($this->hasDstLimits) {
            $field->timezone('UTC')
                ->format('H:i:s')
                ->minDate('2025-03-30 01:30:23')
                ->maxDate('2025-10-26 01:55:47');
        }

        if ($this->hasAppDstGap) {
            $field->timezone('America/New_York');
        }

        return $form
            ->schema([$field])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->saved = $this->form->getState();
        $this->saveCount++;
    }

    public function reloadForm(): void
    {
        $this->form->fill($this->saved);
        $this->reloadCount++;
    }
}
