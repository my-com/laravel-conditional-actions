<?php

namespace ConditionalActions\Entities\Actions;

use ConditionalActions\Contracts\ConditionActionContract;

abstract class BaseConditionAction implements ConditionActionContract
{
    protected $parameters = [];

    /**
     * Sets the action parameters.
     *
     * @param array $parameters
     *
     * @return ConditionActionContract
     */
    public function setParameters(?array $parameters): ConditionActionContract
    {
        $this->parameters = $parameters ?? [];

        return $this;
    }

    /**
     * Gets action parameters.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}
