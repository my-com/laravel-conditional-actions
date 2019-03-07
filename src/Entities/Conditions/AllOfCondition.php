<?php

namespace ConditionalActions\Entities\Conditions;

use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;

class AllOfCondition extends BaseCondition
{
    /**
     * Checks that the condition is met.
     *
     * @param TargetContract $target
     * @param StateContract $state
     *
     * @return bool
     */
    public function check(TargetContract $target, StateContract $state): bool
    {
        $actions = [];

        foreach ($target->getChildrenConditions($this->id) as $condition) {
            $conditionResult = $condition->check($target, $state) !== $condition->isInverted();

            if ($conditionResult !== $this->expectedResult()) {
                return false;
            }
            $actions = \array_merge($actions, $condition->getActions());
        }

        $this->addActions(...$actions);

        return true;
    }
}
