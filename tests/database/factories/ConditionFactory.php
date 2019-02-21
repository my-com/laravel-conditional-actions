<?php

use ConditionalActions\Entities\Eloquent\Condition;
use Faker\Generator;
use Illuminate\Support\Carbon;

$factory->define(Condition::class, function (Generator $faker) {
    return [
        'name' => 'TrueCondition',
    ];
});

$factory->state(Condition::class, 'inactive', function (Generator $faker) {
    return [
        'ends_at' => Carbon::minValue()->toDateTimeString(),
    ];
});
