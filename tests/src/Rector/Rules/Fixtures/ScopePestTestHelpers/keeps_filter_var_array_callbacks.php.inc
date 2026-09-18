<?php

function normalizeValue(string $value): string
{
    return strtoupper($value);
}

it('normalizes values', function () {
    expect(filter_var_array(['value' => 'value'], [
        'value' => [
            'filter' => FILTER_CALLBACK,
            'options' => sprintf('%s%s', 'normalize', 'Value'),
        ],
    ]))->toBe(['value' => 'VALUE']);
});
-----
<?php

function normalizeValue(string $value): string
{
    return strtoupper($value);
}

it('normalizes values', function () {
    expect(filter_var_array(['value' => 'value'], [
        'value' => [
            'filter' => FILTER_CALLBACK,
            'options' => sprintf('%s%s', 'normalize', 'Value'),
        ],
    ]))->toBe(['value' => 'VALUE']);
});
