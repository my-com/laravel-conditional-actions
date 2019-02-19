<?php

namespace ConditionalActions\Contracts;

interface ConditionContract
{
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
     * Gets the actions for the condition.
     *
     * @return iterable|ActionContract[]
     */
    public function getActions(): iterable;

    /**
     * Sets identifier.
     *
     * @param int $id
     *
     * @return ConditionContract
     */
    public function setId(int $id): self;

    /**
     * Gets identifier.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * @param bool|null $isInverted
     *
     * @return ConditionContract
     */
    public function setIsInverted(?bool $isInverted): self;

    /**
     * Determines whether this condition result should be inverted.
     *
     * @return bool
     */
    public function isInverted(): bool;

    /**
     * @param array|null $parameters
     *
     * @return ConditionContract
     */
    public function setParameters(?array $parameters): self;

    /**
     * @return int|null
     */
    public function getParentId(): ?int;

    /**
     * @param int|null $parentId
     *
     * @return ConditionContract
     */
    public function setParentId(?int $parentId): self;
}
