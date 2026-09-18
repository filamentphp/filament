<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

it(require __DIR__ . '/record-description.php', function () {
    expect(true)->toBeTrue();
});
-----
<?php

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

it(require __DIR__ . '/record-description.php', function () {
    expect(true)->toBeTrue();
});
