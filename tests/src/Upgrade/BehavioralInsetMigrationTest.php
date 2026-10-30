<?php

namespace Filament\Tests\Upgrade;

use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Repeater;
use Filament\Tests\TestCase;

use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\factory;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\nullable;
use function Filament\Tests\Upgrade\Fixtures\BehavioralInset\stored;

class BehavioralInsetMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('Filament\\Tests\\Upgrade\\Fixtures\\BehavioralInset\\stored')) {
            [, $migrated] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/behavioral_configuration_inset.php.inc'), 2);
            eval('?>' . $migrated);
        }
    }

    public function test_removes_noop_configuration_and_preserves_fluent_configuration(): void
    {
        $component = Builder::make('blocks');

        $this->assertSame($component, stored($component, true));
        $this->assertSame($component, stored($component, false));
        $this->assertSame($component, stored($component, static function (): bool {
            self::fail('An `inset()` condition must not be evaluated.');
        }));
        $this->assertSame('configured', $component->getLabel());
        $this->assertSame('configured', factory()->getLabel());

        $repeater = Repeater::make('items');
        $this->assertSame($repeater, nullable($repeater));
        $this->assertNull(nullable(null));
    }
}
