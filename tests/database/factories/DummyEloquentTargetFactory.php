<?php

use Faker\Generator;
use Tests\Helpers\Dummy\DummyEloquentTarget;

$factory->define(DummyEloquentTarget::class, function (Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});
