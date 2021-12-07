<?php

use Illuminate\Database\Eloquent\Model;

expect()->extend('toBeSameModel', function (Model $model) {
    return $this
        ->is($model)->toBeTrue();
});
