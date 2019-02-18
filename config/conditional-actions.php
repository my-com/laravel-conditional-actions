<?php

return [
    'conditions' => [
        'AllOf' => ConditionalActions\Entities\Conditions\AllOfCondition::class,
        'OneOf' => ConditionalActions\Entities\Conditions\OneOfCondition::class,
        'True'  => ConditionalActions\Entities\Conditions\TrueCondition::class,
    ],
    'actions' => [
        'UpdateStateAttribute' => ConditionalActions\Entities\Actions\UpdateStateAttributeAction::class,
    ],
];
