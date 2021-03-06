<?php

namespace Tests\Helpers\Dummy;

use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;

class DummyFailedCondition extends DummyCondition
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
        $this->isFired = true;

        return false;
    }
}
