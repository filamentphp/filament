<?php

namespace Filament\Tests\Fixtures\Pages;

use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class ConfigurableSettings extends Page
{
    protected string $view = 'pages.settings';

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 2;

    /**
     * @var class-string<ConfigurableSettingsConfiguration>|null
     */
    protected static ?string $configurationClass = ConfigurableSettingsConfiguration::class;

    public $name;

    public $settingsCategory;

    public function mount(): void
    {
        if ($configuration = static::getConfiguration()) {
            /** @var ConfigurableSettingsConfiguration $configuration */
            $this->settingsCategory = $configuration->getSettingsCategory();
        }
    }

    public static function getNavigationLabel(): string
    {
        if ($configuration = static::getConfiguration()) {
            /** @var ConfigurableSettingsConfiguration $configuration */
            if ($label = $configuration->getNavigationLabel()) {
                return $label;
            }
        }

        return parent::getNavigationLabel();
    }

    public static function getNavigationGroup(): string | UnitEnum | null
    {
        if ($configuration = static::getConfiguration()) {
            /** @var ConfigurableSettingsConfiguration $configuration */
            if ($group = $configuration->getNavigationGroup()) {
                return $group;
            }
        }

        return parent::getNavigationGroup();
    }

    public static function getNavigationSort(): ?int
    {
        if ($configuration = static::getConfiguration()) {
            /** @var ConfigurableSettingsConfiguration $configuration */
            if ($sort = $configuration->getNavigationSort()) {
                return $sort;
            }
        }

        return parent::getNavigationSort();
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->components([
                TextInput::make('name')->required(),
            ]);
    }

    public function save(): void
    {
        $this->form->getState();
    }
}
