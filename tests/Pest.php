<?php

use Illuminate\Database\Eloquent\Model;

expect()->extend('toBeSameModel', function (Model $model) {
    return $this
        ->is($model)->toBeTrue();
});

function set_invalid_utf8_record_attribute(Model $record, string $attribute): Model
{
    $connection = $record->getConnection();

    $connection
        ->table($record->getTable())
        ->where($record->getKeyName(), $record->getKey())
        ->update([
            $attribute => $connection->raw($connection->escape("\xB1\x31", binary: true)),
        ]);

    return $record->refresh();
}

pest()->browser()
    ->timeout(10000);
