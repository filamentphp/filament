<?php

function helper(): string
{
    return 'value';
}

it('accepts a dataset value', function ($helper) {
    expect(helper())->toBe('value');
})->with(['input']);
-----
<?php

function helper(): string
{
    return 'value';
}

it('accepts a dataset value', function ($helper) {
    expect(helper())->toBe('value');
})->with(['input']);
