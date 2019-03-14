<?php

use Faker\Generator;
use Tests\Helpers\Dummy\DummyEloquentModel;

$factory->define(DummyEloquentModel::class, function (Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});
