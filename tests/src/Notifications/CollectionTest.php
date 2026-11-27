<?php

use Filament\Notifications\Collection;
use Filament\Tests\TestCase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(TestCase::class);

it('accepts notification and action nodes at the aggregate restoration limit', function (): void {
    $actions = array_map(
        static fn (int $index): array => ['name' => "action-{$index}"],
        range(1, 99),
    );

    expect(Collection::fromLivewire([[
        'id' => 'at-limit',
        'actions' => $actions,
    ]]))->toHaveCount(1);
});

it('rejects notification and action nodes beyond the aggregate restoration limit', function (): void {
    $actions = array_map(
        static fn (int $index): array => ['name' => "action-{$index}"],
        range(1, 100),
    );

    expect(fn () => Collection::fromLivewire([[
        'id' => 'over-limit',
        'actions' => $actions,
    ]]))->toThrow(fn (HttpException $exception) => expect($exception->getStatusCode())->toBe(419));
});

it('accepts action groups at the maximum nesting depth', function (): void {
    $actions = [['name' => 'leaf']];

    foreach (range(1, 10) as $depth) {
        $actions = [['actions' => $actions, 'isOutlined' => false]];
    }

    expect(Collection::fromLivewire([[
        'id' => 'at-depth-limit',
        'actions' => $actions,
    ]]))->toHaveCount(1);
});

it('rejects action groups beyond the maximum nesting depth', function (): void {
    $actions = [['name' => 'leaf']];

    foreach (range(1, 11) as $depth) {
        $actions = [['actions' => $actions, 'isOutlined' => false]];
    }

    expect(fn () => Collection::fromLivewire([[
        'id' => 'over-depth-limit',
        'actions' => $actions,
    ]]))->toThrow(fn (HttpException $exception) => expect($exception->getStatusCode())->toBe(419));
});

it('shares the restoration allowance across calls in one request', function (): void {
    Collection::fromLivewire([[
        'id' => 'first',
        'actions' => array_map(
            static fn (int $index): array => ['name' => "first-action-{$index}"],
            range(1, 59),
        ),
    ]]);

    expect(fn () => Collection::fromLivewire([[
        'id' => 'second',
        'actions' => array_map(
            static fn (int $index): array => ['name' => "second-action-{$index}"],
            range(1, 40),
        ),
    ]]))->toThrow(fn (HttpException $exception) => expect($exception->getStatusCode())->toBe(419));
});
