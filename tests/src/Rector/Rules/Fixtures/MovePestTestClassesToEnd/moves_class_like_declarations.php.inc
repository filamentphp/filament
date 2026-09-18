<?php

// A helper used by the tests.
class HelperClass
{
}

trait HelperTrait
{
}

enum HelperEnum
{
    case Value;
}

it('uses the helpers', function (): void {
    expect(new HelperClass())->toBeInstanceOf(HelperClass::class);
});
-----
<?php

it('uses the helpers', function (): void {
    expect(new HelperClass())->toBeInstanceOf(HelperClass::class);
});

// A helper used by the tests.
class HelperClass
{
}


trait HelperTrait
{
}


enum HelperEnum
{
    case Value;
}
