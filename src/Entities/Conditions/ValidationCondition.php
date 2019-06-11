<?php

namespace ConditionalActions\Entities\Conditions;

use ConditionalActions\ConditionalActionException;
use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;
use ConditionalActions\Contracts\TargetProviders\ProvidesValidationData;
use Illuminate\Support\Facades\Validator;

class ValidationCondition extends BaseCondition
{
    /**
     * Runs condition check.
     *
     * @param TargetContract|ProvidesValidationData $target
     * @param StateContract $state
     *
     * @throws ConditionalActionException
     *
     * @return bool
     */
    public function check(TargetContract $target, StateContract $state): bool
    {
        if (!($target instanceof ProvidesValidationData)) {
            throw new ConditionalActionException(
                'The target does not implemented ProvidesValidationData contract',
                400
            );
        }

        $validator = Validator::make($target->getValidationData(), $this->parameters ?? []);

        return !$validator->fails();
    }
}
