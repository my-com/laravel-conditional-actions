<?php

namespace Tests\Dummy;

use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;

class DummySucceedCondition extends DummyCondition
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

        return true;
    }
}
