<?php

use ConditionalActions\Entities\Eloquent\Condition;
use Faker\Generator;

$factory->define(Condition::class, function (Generator $faker) {
    return [
        'name' => 'True',
    ];
});
