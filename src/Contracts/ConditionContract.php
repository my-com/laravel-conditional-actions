<?php

namespace ConditionalActions\Contracts;

use ConditionalActions\Entities\Conditions\BaseCondition;
use Illuminate\Support\Carbon;

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
     * Sets actions.
     *
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
     * Sets inverted flag.
     *
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
     * Sets the condition parameters.
     *
     * @param array|null $parameters
     *
     * @return ConditionContract
     */
    public function setParameters(?array $parameters): self;

    /**
     * Gets parent identifier.
     *
     * @return int|null
     */
    public function getParentId(): ?int;

    /**
     * Sets parent identifier.
     *
     * @param int|null $parentId
     *
     * @return ConditionContract
     */
    public function setParentId(?int $parentId): self;

    /**
     * Gets condition parameters.
     *
     * @return iterable
     */
    public function getParameters(): iterable;

    /**
     * Gets priority.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Sets the priority.
     *
     * @param int $priority
     *
     * @return BaseCondition
     */
    public function setPriority(int $priority): BaseCondition;

    /**
     * Sets the starts time.
     *
     * @param Carbon|null $startsAt
     *
     * @return BaseCondition
     */
    public function setStartsAt(?Carbon $startsAt): BaseCondition;

    /**
     * Gets finishes time.
     *
     * @return Carbon|null
     */
    public function getEndsAt(): ?Carbon;

    /**
     * Gets starts time.
     *
     * @return Carbon|null
     */
    public function getStartsAt(): ?Carbon;

    /**
     * Sets the finishes time.
     *
     * @param Carbon|null $endsAt
     *
     * @return BaseCondition
     */
    public function setEndsAt(?Carbon $endsAt): BaseCondition;
}
