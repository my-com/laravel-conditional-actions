<?php

namespace ConditionalActions\Entities\Conditions;

use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;

class OneOfCondition extends BaseCondition
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

            $conditionResult = $condition->check($target, $state) !== $condition->isInverted();

            if ($conditionResult === $this->expectedResult()) {
                $this->addActions(...$condition->getActions());

                return true;
            }
        }

        return false;
    }
}
