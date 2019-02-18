<?php

namespace ConditionalActions\Contracts;

interface ActionContract
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
     * @return ActionContract
     */
    public function setParameters(?array $parameters): self;
}
