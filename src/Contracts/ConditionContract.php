<?php

namespace ConditionalActions\Contracts;

interface ConditionContract
{
    /**
     * Determines whether this condition result should be inverted.
     *
     * @return bool
     */
    public function isInverted(): bool;

    /**
     * Gets the actions for the condition.
     *
     * @return iterable|ActionContract[]
     */
    public function getActions(): iterable;

    /**
     * Checks that the condition is met.
     *
     * @param TargetContract $target
     * @param StateContract $state
     *
     * @return bool
     */
    public function check(TargetContract $target, StateContract $state): bool;

    /**
     * @param iterable|null $actions
     *
     * @return ConditionContract
     */
    public function setActions(?iterable $actions): self;

    /**
     * @param int $id
     *
     * @return ConditionContract
     */
    public function setId(int $id): self;

    /**
     * @param bool|null $isInverted
     *
     * @return ConditionContract
     */
    public function setIsInverted(?bool $isInverted): self;

    /**
     * @param array|null $parameters
     *
     * @return ConditionContract
     */
    public function setParameters(?array $parameters): self;
}
