<?php

if (!function_exists('create')) {
    function create($model, $attributes = [], string ...$states) {
        return factory($model)->states($states)->create($attributes);
    }
}

if (!function_exists('createMany')) {
    function createMany($model, $keys = [], $attributesTable = [], $baseRow = [], string ...$states) {
        $collection = new \Illuminate\Database\Eloquent\Collection();
        foreach ($attributesTable as $attributes) {
            $collection->push(create($model, array_merge($baseRow, array_combine($keys, $attributes)), ...$states));
        }

        return $collection;
    }
}
