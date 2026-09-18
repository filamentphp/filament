<?php

namespace App\Tests;

function namespacedHelper(): string
{
    return 'namespaced';
}

it('uses the namespaced helper', function () {
    expect(namespacedHelper())->toBe('namespaced');
});
-----
<?php

namespace App\Tests;

function namespacedHelper(): string
{
    return 'namespaced';
}

it('uses the namespaced helper', function () {
    expect(namespacedHelper())->toBe('namespaced');
});