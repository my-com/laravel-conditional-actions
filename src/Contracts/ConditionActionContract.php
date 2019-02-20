<?php

namespace ConditionalActions\Contracts;

interface ConditionActionContract
{
    /**
     * Applies action to the state and returns a new state.
     *
     * @param StateContract $state
     *
     * @return StateContract
     */
    public function apply(StateContract $state): StateContract;

    /**
     * Sets the action parameters.
     *
     * @param array $parameters
     *
     * @return ConditionActionContract
     */
    public function setParameters(?array $parameters): self;

    /**
     * Gets action parameters.
     *
     * @return array
     */
    public function getParameters(): array;
}
