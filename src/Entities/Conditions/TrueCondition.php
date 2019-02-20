<?php

namespace ConditionalActions\Entities\Conditions;

use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;

class TrueCondition extends BaseCondition
{
    /**
     * Runs condition check.
     *
     * @param TargetContract $target
     * @param StateContract $state
     *
     * @return bool
     */
    public function check(TargetContract $target, StateContract $state): bool
    {
        return true;
    }
}
