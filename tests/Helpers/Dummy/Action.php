<?php

namespace Tests\Helpers\Dummy;

use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Entities\Actions\BaseConditionAction;

class Action extends BaseConditionAction implements CanBeFired
{
    public $isFired = false;

    /**
     * Applies action to the state and returns a new state.
     *
     * @param StateContract $state
     *
     * @return StateContract
     */
    public function apply(StateContract $state): StateContract
    {
        $this->isFired = true;

        return $state;
    }

    public function isFired(): bool
    {
        return $this->isFired;
    }
}
