<?php

function replacementValue(array $matches): string
{
    return strtoupper($matches[0]);
}

it('replaces matches', function () {
    expect(preg_replace_callback_array(
        ['/a/' => sprintf('%s%s', 'replacement', 'Value')],
        'a',
    ))->toBe('A');
});
-----
<?php

function replacementValue(array $matches): string
{
    return strtoupper($matches[0]);
}

it('replaces matches', function () {
    expect(preg_replace_callback_array(
        ['/a/' => sprintf('%s%s', 'replacement', 'Value')],
        'a',
    ))->toBe('A');
});
