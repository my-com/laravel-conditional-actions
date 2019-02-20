<?php

use ConditionalActions\Entities\Eloquent\Condition;
use ConditionalActions\Entities\Eloquent\ConditionAction;
use Faker\Generator;
use Illuminate\Support\Carbon;

$factory->define(ConditionAction::class, function (Generator $faker) {
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
