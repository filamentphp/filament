<?php

function helper(): string
{
    return 'value';
}

$helper = fn (): string => 'other';

it('uses both callables', function () use ($helper) {
    expect(helper())->toBe('value')
        ->and($helper())->toBe('other');
});
-----
<?php

function helper(): string
{
    return 'value';
}

$helper = fn (): string => 'other';

it('uses both callables', function () use ($helper) {
    expect(helper())->toBe('value')
        ->and($helper())->toBe('other');
});
