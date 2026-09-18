<?php

function normalizeValue(string $value): string
{
    return strtoupper($value);
}

it('normalizes a value', function () {
    expect(filter_var('value', FILTER_CALLBACK, [
        'options' => sprintf('%s%s', 'normalize', 'Value'),
    ]))->toBe('VALUE');
});
-----
<?php

function normalizeValue(string $value): string
{
    return strtoupper($value);
}

it('normalizes a value', function () {
    expect(filter_var('value', FILTER_CALLBACK, [
        'options' => sprintf('%s%s', 'normalize', 'Value'),
    ]))->toBe('VALUE');
});
