<?php

namespace Filament\Tests\Upgrade;

use Filament\Actions\Action;
use Filament\Panel;
use Filament\Tests\TestCase;

use function Filament\Tests\Upgrade\Fixtures\MenuItemsDynamic\register;

class MenuItemsMigrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('Filament\\Tests\\Upgrade\\Fixtures\\MenuItemsDynamic\\register')) {
            [, $migrated] = explode("-----\n", file_get_contents(__DIR__ . '/Fixtures/menu_items_dynamic.php.inc'), 2);
            eval('?>' . $migrated);
        }
    }

    public function test_preserves_eager_arguments_lazy_callbacks_and_builtin_customization(): void
    {
        $panel = new class extends Panel
        {
            public array $registeredItems = [];

            public function userMenuItems(array $items): static
            {
                $this->registeredItems = $items;

                return $this;
            }
        };
        $events = [];
        $hidden = true;
        $label = 'Before';
        register($panel, $events, $hidden, $label);

        $this->assertSame(['snapshot', '/old', 'Help'], $events);
        $label = 'After';
        $action = Action::make('profile')->label('Default')->icon('default-icon')->sort(-1)->url('/default');
        $this->assertSame($action, $panel->registeredItems['profile']($action));
        $this->assertSame('profile', $action->getName());
        $this->assertSame('After', $action->getLabel());
        $this->assertNull($action->getIcon());
        $this->assertSame(0, $action->getSort());
        $this->assertSame('/post', $action->getUrl());
        $this->assertTrue($action->shouldPostToUrl());
        $this->assertTrue($action->shouldOpenUrlInNewTab());
        $this->assertFalse($action->isVisible());
        $this->assertSame(['snapshot', '/old', 'Help', 'label', 'post', 'hidden'], $events);

        $hidden = false;
        $panel->registeredItems['profile']($action);
        $this->assertTrue($action->isVisible());
        $this->assertSame(['snapshot', '/old', 'Help', 'label', 'post', 'hidden', 'label', 'post', 'hidden', 'visible'], $events);

        $help = $panel->registeredItems[0]();
        $this->assertSame('help', $help->getName());
        $this->assertSame('Help', $help->getLabel());
        $this->assertSame('/help', $help->getUrl());
        $this->assertSame(-3, $help->getSort());
    }
}
