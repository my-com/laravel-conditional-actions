<?php

use ConditionalActions\Entities\Eloquent\Condition;
use ConditionalActions\Entities\Eloquent\ConditionAction;
use Faker\Generator;

$factory->define(ConditionAction::class, function (Generator $faker) {
    return [
        'condition_id' => factory(Condition::class),
        'name' => 'UpdateStateAttribute',
    ];
});
