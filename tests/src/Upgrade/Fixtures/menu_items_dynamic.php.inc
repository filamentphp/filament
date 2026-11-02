<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItemsDynamic;

use Filament\Actions\Action;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Components\Component;

function register(Panel $panel, array &$events, bool &$hidden, string &$label): void
{
    $eager = static function (string $value) use (&$events): string {
        $events[] = $value;

        return $value;
    };
    $panel->userMenuItems([
        'profile' => MenuItem::make()
            ->label($eager('snapshot'))
            ->label(static function () use (&$events, &$label): string {
                $events[] = 'label';

                return $label;
            })
            ->postAction(static function () use (&$events): string {
                $events[] = 'post';

                return '/post';
            })
            ->url(shouldOpenInNewTab: true, url: $eager('/old'))
            ->url(url: static fn (): ?string => null, shouldOpenInNewTab: null)
            ->hidden(static function () use (&$events, &$hidden): bool {
                $events[] = 'hidden';

                return $hidden;
            })
            ->visible(static function () use (&$events): bool {
                $events[] = 'visible';

                return true;
            }),
        MenuItem::make()->label($eager('Help'))->url('/help')->sort(static fn (Component $component): int => $component instanceof Action ? 100 : -3),
    ]);
}
-----
<?php

namespace Filament\Tests\Upgrade\Fixtures\MenuItemsDynamic;

use Closure;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\Support\Components\Component;

function register(Panel $panel, array &$events, bool &$hidden, string &$label): void
{
    $eager = static function (string $value) use (&$events): string {
        $events[] = $value;

        return $value;
    };
    $panel->userMenuItems([
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
        })([['label', ['label' => $eager('snapshot')]], ['label', ['label' => static function () use (&$events, &$label): string {
            $events[] = 'label';

            return $label;
        }]], ['postAction', ['action' => static function () use (&$events): string {
            $events[] = 'post';

            return '/post';
        }]], ['url', ['shouldOpenInNewTab' => true, 'url' => $eager('/old')]], ['url', ['url' => static fn (): ?string => null, 'shouldOpenInNewTab' => null]], ['hidden', ['condition' => static function () use (&$events, &$hidden): bool {
            $events[] = 'hidden';

            return $hidden;
        }]], ['visible', ['condition' => static function () use (&$events): bool {
            $events[] = 'visible';

            return true;
        }]]]),
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
        })([['label', ['label' => $eager('Help')]], ['url', ['url' => '/help']], ['sort', ['sort' => static fn (Component $component): int => $component instanceof Action ? 100 : -3]]]),
    ]);
}
