<?php

namespace Filament\Tests\Schemas\Components;

use Filament\Schemas\Components\Component;
use Filament\Support\Components\Attributes\ExposedLivewireMethod;
use Filament\Support\Components\ComponentManager;
use Filament\Tests\TestCase;

uses(TestCase::class);

it('discovers only public instance methods with `ExposedLivewireMethod` independently of Blade extraction order', function (bool $bladeFirst): void {
    // Use distinct concrete classes so both orders start with cold caches.
    $component = $bladeFirst
        ? new class extends ExposedMethodsChild {}
    : new class extends ExposedMethodsChild {};
    $manager = app(ComponentManager::class);
    if ($bladeFirst) {
        $manager->extractPublicMethods($component);
    }
    expect($component->getExposedLivewireMethodNames())->toEqualCanonicalizing(['inherited', 'fluent']);
    $methods = $manager->extractPublicMethods($component);
    expect($methods['inherited']())->toBe('Inherited')
        ->and($methods)->not->toHaveKey('fluent')
        ->and($component->getExposedLivewireMethodNames())->toEqualCanonicalizing(['inherited', 'fluent']);
    $other = new ExposedMethodsParent;
    expect($other->getExposedLivewireMethodNames())->toEqualCanonicalizing(['inherited', 'overridden']);
})->with([false, true]);

class ExposedMethodsParent extends Component
{
    #[ExposedLivewireMethod]
    public function inherited(): string
    {
        return 'Inherited';
    }

    #[ExposedLivewireMethod]
    public function overridden(): string
    {
        return 'Parent';
    }

    public function ordinary(): void {}
}

class ExposedMethodsChild extends ExposedMethodsParent
{
    public function overridden(): string
    {
        return 'Not exposed';
    }

    #[ExposedLivewireMethod]
    public function fluent(): static
    {
        return $this;
    }

    #[ExposedLivewireMethod]
    protected function protectedMethod(): void {}

    #[ExposedLivewireMethod]
    private function privateMethod(): void {}

    #[ExposedLivewireMethod]
    public static function staticMethod(): void {}
}
