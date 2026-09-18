<?php

function helper(): string
{
    return 'ok';
}

it('preserves helper calls', function () {
    $variables = [sprintf('%s%s', 'hel', 'per') => 42];

    extract($variables);

    expect(helper())->toBe('ok');
});
-----
<?php

function helper(): string
{
    return 'ok';
}

it('preserves helper calls', function () {
    $variables = [sprintf('%s%s', 'hel', 'per') => 42];

    extract($variables);

    expect(helper())->toBe('ok');
});
