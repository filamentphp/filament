<?php

namespace Filament\Tests\Panels\Commands;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Filament\Tests\TestCase;
use Illuminate\Support\Arr;
use Spatie\LaravelSettings\Settings as BaseSettings;

use function PHPUnit\Framework\assertFileExists;

uses(TestCase::class);

beforeEach(function (): void {
    $this->withoutMockingConsoleOutput();
})
    ->skip((bool) Arr::get($_SERVER, 'PARATEST'), 'File generation tests cannot be run in parallel as they would share a filesystem and have the potential to conflict with each other.');

it('can generate a page class', function (): void {
    $this->artisan('make:filament-settings-page', [
        'name' => 'ManageSettings',
        'settings' => Settings::class,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Pages/ManageSettings.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page class in a nested directory', function (): void {
    $this->artisan('make:filament-settings-page', [
        'name' => 'Site/ManageSettings',
        'settings' => Settings::class,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Pages/Site/ManageSettings.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page class in a cluster', function (): void {
    $this->artisan('make:filament-cluster', [
        'name' => 'Site',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    require_once app_path('Filament/Clusters/Site/SiteCluster.php');

    $this->artisan('make:filament-settings-page', [
        'name' => 'ManageSettings',
        'settings' => Settings::class,
        '--cluster' => 'App\\Filament\\Clusters\\Site\\SiteCluster',
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Clusters/Site/Pages/ManageSettings.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

it('can generate a page class with a generated form schema', function (): void {
    $this->artisan('make:filament-settings-page', [
        'name' => 'ManageSettings',
        'settings' => Settings::class,
        '--generate' => true,
        '--panel' => 'admin',
        '--no-interaction' => true,
    ]);

    assertFileExists($path = app_path('Filament/Pages/ManageSettings.php'));
    expect(file_get_contents($path))
        ->toMatchSnapshot();
});

class Settings extends BaseSettings
{
    public string $string;

    public ?string $nullableString;

    public bool $bool;

    public int $int;

    public ?int $nullableInt;

    public float $float;

    public ?float $nullableFloat;

    public DateTimeInterface $dateTimeInterface;

    public DateTime $dateTime;

    public DateTimeImmutable $dateTimeImmutable;

    public CarbonInterface $carbonInterface;

    public Carbon $carbon;

    public CarbonImmutable $carbonImmutable;

    public ?DateTimeInterface $nullableDateTimeInterface;

    public ?DateTime $nullableDateTime;

    public ?DateTimeImmutable $nullableDateTimeImmutable;

    public ?CarbonInterface $nullableCarbonInterface;

    public ?Carbon $nullableCarbon;

    public ?CarbonImmutable $nullableCarbonImmutable;

    public SettingsPropertyEnum $enum;

    public ?SettingsPropertyEnum $nullableEnum;

    public string $image;

    public ?string $nullableImage;

    public string $id;

    public float $cost;

    public static function group(): string
    {
        return 'settings';
    }
}

enum SettingsPropertyEnum: string
{
    case One = 'one';

    case Two = 'two';

    case Three = 'three';
}
