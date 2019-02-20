<?php

namespace ConditionalActions\Entities\Actions;

use ConditionalActions\Contracts\StateContract;

class UpdateStateAttributeAction extends BaseConditionAction
{
    public function apply(StateContract $state): StateContract
    {
        foreach ($this->parameters as $key => $value) {
            $state->setAttribute($key, $value);
        }

        return $state;
    }
}
