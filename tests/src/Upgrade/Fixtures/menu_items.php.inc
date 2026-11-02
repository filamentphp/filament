<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItems;

use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;

function register(Panel $panel): void
{
    $panel->userMenuItems([
        [
            'account' => MenuItem::make()->label('My profile'),
            MenuItem::make()->label('Help & support')->color('warning')->url('/help', true)->openUrlInNewTab(false)->sort(+3),
        ],
        [
            MenuItem::make()->label('Hidden')->hidden()->visible(true),
            'logout' => MenuItem::make()->label('Exit')->postAction('/exit')->url(''),
        ],
    ]);
    $panel->tenantMenuItems([
        'profile' => MenuItem::make()->label('Organization')->sort(-1),
        'billing' => MenuItem::make()->label('Plan')->visible(false),
    ]);
}

function legacyRegistration(): void
{
    Filament::registerUserMenuItems([MenuItem::make()->label('Docs')->url('/docs')]);
}

function manual(Panel $panel, string $label): void
{
    $panel->userMenuItems([
        MenuItem::make()->label($label),
        MenuItem::make()->label(fn () => 'Dynamic'),
    ]);
    $stored = MenuItem::make()->label('Stored');
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItems;

use Closure;
use Filament\Actions\Action;
use Filament\Support\Components\Component;
use Illuminate\Support\Str;
use Filament\Exceptions\NoDefaultPanelSetException;
use LogicException;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Panel;

function register(Panel $panel): void
{
    $panel->userMenuItems([
        [
            'account' => (static function (array $calls): Closure {
                $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
                foreach ($calls as [$method, $arguments]) {
                    if ($method === 'url') {
                        if (isset($arguments['shouldOpenInNewTab'])) {
                            $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                        }
                        $configuration['url'] = $arguments['url'];
                    } else {
                        $parameter = match ($method) {
                            'postAction' => 'action',
                            'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                            default => $method,
                        };
                        $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
                    }
                }
                return static function (Action $action) use ($configuration): Action {
                    $evaluator = new class extends Component
                    {
                    };
                    $label = $evaluator->evaluate($configuration['label']);
                    $postAction = $evaluator->evaluate($configuration['postAction']);
                    return $action->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
                };
            })([['label', ['label' => 'My profile']]]),
            (static function (array $calls): Closure {
                $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
                foreach ($calls as [$method, $arguments]) {
                    if ($method === 'url') {
                        if (isset($arguments['shouldOpenInNewTab'])) {
                            $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                        }
                        $configuration['url'] = $arguments['url'];
                    } else {
                        $parameter = match ($method) {
                            'postAction' => 'action',
                            'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                            default => $method,
                        };
                        $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
                    }
                }
                return static function () use ($configuration): Action {
                    $evaluator = new class extends Component
                    {
                    };
                    $label = $evaluator->evaluate($configuration['label']);
                    $postAction = $evaluator->evaluate($configuration['postAction']);
                    return Action::make(Str::slug(Str::transliterate($label, strict: true)))->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
                };
            })([['label', ['label' => 'Help & support']], ['color', ['color' => 'warning']], ['url', ['url' => '/help', 'shouldOpenInNewTab' => true]], ['openUrlInNewTab', ['condition' => false]], ['sort', ['sort' => +3]]]),
        ],
        [
            (static function (array $calls): Closure {
                $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
                foreach ($calls as [$method, $arguments]) {
                    if ($method === 'url') {
                        if (isset($arguments['shouldOpenInNewTab'])) {
                            $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                        }
                        $configuration['url'] = $arguments['url'];
                    } else {
                        $parameter = match ($method) {
                            'postAction' => 'action',
                            'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                            default => $method,
                        };
                        $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
                    }
                }
                return static function () use ($configuration): Action {
                    $evaluator = new class extends Component
                    {
                    };
                    $label = $evaluator->evaluate($configuration['label']);
                    $postAction = $evaluator->evaluate($configuration['postAction']);
                    return Action::make(Str::slug(Str::transliterate($label, strict: true)))->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
                };
            })([['label', ['label' => 'Hidden']], ['hidden', []], ['visible', ['condition' => true]]]),
            'logout' => (static function (array $calls): Closure {
                $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
                foreach ($calls as [$method, $arguments]) {
                    if ($method === 'url') {
                        if (isset($arguments['shouldOpenInNewTab'])) {
                            $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                        }
                        $configuration['url'] = $arguments['url'];
                    } else {
                        $parameter = match ($method) {
                            'postAction' => 'action',
                            'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                            default => $method,
                        };
                        $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
                    }
                }
                return static function (Action $action) use ($configuration): Action {
                    $evaluator = new class extends Component
                    {
                    };
                    $label = $evaluator->evaluate($configuration['label']);
                    $postAction = $evaluator->evaluate($configuration['postAction']);
                    return $action->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
                };
            })([['label', ['label' => 'Exit']], ['postAction', ['action' => '/exit']], ['url', ['url' => '']]]),
        ],
    ]);
    $panel->tenantMenuItems([
        'profile' => (static function (array $calls): Closure {
            $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
            foreach ($calls as [$method, $arguments]) {
                if ($method === 'url') {
                    if (isset($arguments['shouldOpenInNewTab'])) {
                        $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                    }
                    $configuration['url'] = $arguments['url'];
                } else {
                    $parameter = match ($method) {
                        'postAction' => 'action',
                        'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                        default => $method,
                    };
                    $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
                }
            }
            return static function (Action $action) use ($configuration): Action {
                $evaluator = new class extends Component
                {
                };
                $label = $evaluator->evaluate($configuration['label']);
                $postAction = $evaluator->evaluate($configuration['postAction']);
                return $action->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
            };
        })([['label', ['label' => 'Organization']], ['sort', ['sort' => -1]]]),
        'billing' => (static function (array $calls): Closure {
            $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
            foreach ($calls as [$method, $arguments]) {
                if ($method === 'url') {
                    if (isset($arguments['shouldOpenInNewTab'])) {
                        $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                    }
                    $configuration['url'] = $arguments['url'];
                } else {
                    $parameter = match ($method) {
                        'postAction' => 'action',
                        'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                        default => $method,
                    };
                    $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
                }
            }
            return static function (Action $action) use ($configuration): Action {
                $evaluator = new class extends Component
                {
                };
                $label = $evaluator->evaluate($configuration['label']);
                $postAction = $evaluator->evaluate($configuration['postAction']);
                return $action->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
            };
        })([['label', ['label' => 'Plan']], ['visible', ['condition' => false]]]),
    ]);
}

function legacyRegistration(): void
{
    (static function (array $items): void {
        try {
            Filament::getDefaultPanel()->userMenuItems($items);
        } catch (NoDefaultPanelSetException $exception) {
            throw new LogicException('Please use the `userMenuItems()` method on the panel configuration to register user menu items. See the documentation - https://filamentphp.com/docs/panels/navigation#customizing-the-user-menu');
        }
    })([(static function (array $calls): Closure {
        $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
        foreach ($calls as [$method, $arguments]) {
            if ($method === 'url') {
                if (isset($arguments['shouldOpenInNewTab'])) {
                    $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                }
                $configuration['url'] = $arguments['url'];
            } else {
                $parameter = match ($method) {
                    'postAction' => 'action',
                    'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                    default => $method,
                };
                $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
            }
        }
        return static function () use ($configuration): Action {
            $evaluator = new class extends Component
            {
            };
            $label = $evaluator->evaluate($configuration['label']);
            $postAction = $evaluator->evaluate($configuration['postAction']);
            return Action::make(Str::slug(Str::transliterate($label, strict: true)))->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
        };
    })([['label', ['label' => 'Docs']], ['url', ['url' => '/docs']]])]);
}

function manual(Panel $panel, string $label): void
{
    $panel->userMenuItems([
        (static function (array $calls): Closure {
            $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
            foreach ($calls as [$method, $arguments]) {
                if ($method === 'url') {
                    if (isset($arguments['shouldOpenInNewTab'])) {
                        $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                    }
                    $configuration['url'] = $arguments['url'];
                } else {
                    $parameter = match ($method) {
                        'postAction' => 'action',
                        'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                        default => $method,
                    };
                    $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
                }
            }
            return static function () use ($configuration): Action {
                $evaluator = new class extends Component
                {
                };
                $label = $evaluator->evaluate($configuration['label']);
                $postAction = $evaluator->evaluate($configuration['postAction']);
                return Action::make(Str::slug(Str::transliterate($label, strict: true)))->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
            };
        })([['label', ['label' => $label]]]),
        (static function (array $calls): Closure {
            $configuration = ['color' => null, 'icon' => null, 'label' => null, 'postAction' => null, 'sort' => null, 'url' => null, 'hidden' => false, 'visible' => true, 'openUrlInNewTab' => false];
            foreach ($calls as [$method, $arguments]) {
                if ($method === 'url') {
                    if (isset($arguments['shouldOpenInNewTab'])) {
                        $configuration['openUrlInNewTab'] = $arguments['shouldOpenInNewTab'];
                    }
                    $configuration['url'] = $arguments['url'];
                } else {
                    $parameter = match ($method) {
                        'postAction' => 'action',
                        'hidden', 'visible', 'openUrlInNewTab' => 'condition',
                        default => $method,
                    };
                    $configuration[$method] = $arguments[$parameter] ?? (in_array($method, ['hidden', 'visible', 'openUrlInNewTab'], true) ? true : null);
                }
            }
            return static function () use ($configuration): Action {
                $evaluator = new class extends Component
                {
                };
                $label = $evaluator->evaluate($configuration['label']);
                $postAction = $evaluator->evaluate($configuration['postAction']);
                return Action::make(Str::slug(Str::transliterate($label, strict: true)))->color($evaluator->evaluate($configuration['color']))->icon($evaluator->evaluate($configuration['icon']))->label($label)->postToUrl(filled($postAction))->sort($evaluator->evaluate($configuration['sort']) ?? 0)->visible(!$evaluator->evaluate($configuration['hidden']) && $evaluator->evaluate($configuration['visible']))->url($evaluator->evaluate($configuration['url']) ?? $postAction, (bool) $evaluator->evaluate($configuration['openUrlInNewTab']));
            };
        })([['label', ['label' => fn () => 'Dynamic']]]),
    ]);
    $stored = MenuItem::make()->label('Stored');
}
