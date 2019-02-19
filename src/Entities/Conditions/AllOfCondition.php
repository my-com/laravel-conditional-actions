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
        foreach ($target->getChildrenConditions($this->id) as $condition) {
            if ($condition->check($target, $state) !== $this->expectedResult()) {
                return false;
            } else {
                $this->addActions(...$condition->getActions());
            }
        }

        return true;
    }
}
