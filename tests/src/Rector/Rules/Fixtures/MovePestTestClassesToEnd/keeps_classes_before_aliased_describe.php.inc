<?php

namespace Tests\AliasReproducer;

use function describe as test;

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

test('records', function () {
    $record = new LocalRecord;

    \it('has a record', fn () => expect($record)
        ->toBeInstanceOf(LocalRecord::class));
});
-----
<?php

namespace Tests\AliasReproducer;

use function describe as test;

trait LocalTrait
{
}

class LocalRecord
{
    use LocalTrait;
}

test('records', function () {
    $record = new LocalRecord;

    \it('has a record', fn () => expect($record)
        ->toBeInstanceOf(LocalRecord::class));
});
