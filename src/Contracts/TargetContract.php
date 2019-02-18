<?php

namespace ConditionalActions\Contracts;

interface TargetContract
{
    /**
     * Gets state from target.
     *
     * @return StateContract
     */
    public function getState(): StateContract;

    /**
     * Sets the state to the target.
     *
     * @param StateContract $state
     */
    public function setState(StateContract $state): void;

    /**
     * Gets root target conditions.
     *
     * @return iterable|ConditionContract[]
     */
    public function getRootConditions(): iterable;

    /**
     * Gets children target conditions.
     *
     * @param int $parentId
     *
     * @return iterable|ConditionContract[]
     */
    public function getChildrenConditions(int $parentId): iterable;
}
