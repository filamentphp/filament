<?php

namespace Tests\Fixtures;

trait HelperTrait
{
}

namespace Tests;

it('runs a test', function (): void {
    expect(true)->toBeTrue();
});

class HelperClass
{
}
-----
<?php

namespace Tests\Fixtures;

trait HelperTrait
{
}

namespace Tests;

it('runs a test', function (): void {
    expect(true)->toBeTrue();
});

class HelperClass
{
}
