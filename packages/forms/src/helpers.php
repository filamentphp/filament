<?php

namespace Filament\Forms;

if (! function_exists('Filament\Forms\array_move_after')) {
    /**
     * @param  array<mixed>  $array
     * @param  scalar  $keyToMoveAfter
     * @return array<mixed>
     */
    function array_move_after(array $array, $keyToMoveAfter): array
    {
        $keys = array_keys($array);

        $indexToMoveAfter = array_search($keyToMoveAfter, $keys);
        $keyToMoveBefore = $keys[$indexToMoveAfter + 1] ?? null;

        if (blank($keyToMoveBefore)) {
            return $array;
        }

        $keys[$indexToMoveAfter + 1] = $keyToMoveAfter;
        $keys[$indexToMoveAfter] = $keyToMoveBefore;

        $newArray = [];

        foreach ($keys as $key) {
            $newArray[$key] = $array[$key];
        }

        return $newArray;
    }
}

if (! function_exists('Filament\Forms\array_move_before')) {
    /**
     * @param  array<mixed>  $array
     * @param  array-key  $keyToMoveBefore
     * @return array<mixed>
     */
    function array_move_before(array $array, $keyToMoveBefore): array
    {
        $keys = array_keys($array);

        $indexToMoveBefore = array_search($keyToMoveBefore, $keys);
        $keyToMoveAfter = $keys[$indexToMoveBefore - 1] ?? null;

        if (blank($keyToMoveAfter)) {
            return $array;
        }

        $keys[$indexToMoveBefore - 1] = $keyToMoveBefore;
        $keys[$indexToMoveBefore] = $keyToMoveAfter;

        $newArray = [];

        foreach ($keys as $key) {
            $newArray[$key] = $array[$key];
        }

        return $newArray;
    }
}
