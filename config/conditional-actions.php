<?php

return [
    'conditions' => [
        'AllOfCondition' => ConditionalActions\Entities\Conditions\AllOfCondition::class,
        'OneOfCondition' => ConditionalActions\Entities\Conditions\OneOfCondition::class,
        'TrueCondition' => ConditionalActions\Entities\Conditions\TrueCondition::class,
        'ValidationCondition' => ConditionalActions\Entities\Conditions\ValidationCondition::class,
    ],
    'actions' => [
        'UpdateStateAttributeAction' => ConditionalActions\Entities\Actions\UpdateStateAttributeAction::class,
    ],
    'use_logger' => env('APP_DEBUG', false),
];
