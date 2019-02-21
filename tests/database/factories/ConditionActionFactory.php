<?php

use ConditionalActions\Entities\Eloquent\Condition;
use ConditionalActions\Entities\Eloquent\Action;
use Faker\Generator;
use Illuminate\Support\Carbon;

$factory->define(Action::class, function (Generator $faker) {
    return [
        'condition_id' => factory(Condition::class),
        'name' => 'UpdateStateAttribute',
    ];
});

$factory->state(Condition::class, 'inactive', function (Generator $faker) {
    return [
        'ends_at' => Carbon::minValue()->toDateTimeString(),
    ];
});
