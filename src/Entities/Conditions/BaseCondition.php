<?php

namespace ConditionalActions\Entities\Conditions;

use ConditionalActions\Contracts\ActionContract;
use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;

abstract class BaseCondition implements ConditionContract
{
    /** @var int */
    protected $id;

    /** @var bool|null */
    protected $isInverted = false;

    /** @var iterable|null */
    protected $actions = [];

    /** @var iterable */
    protected $parameters = [];

    /** @var int|null */
    protected $parentId;

    /**
     * Checks that the condition is met.
     *
     * @param TargetContract $target
     * @param StateContract $state
     *
     * @return bool
     */
    abstract public function check(TargetContract $target, StateContract $state): bool;

    /**
     * @param int $id
     *
     * @return ConditionContract
     */
    public function setId(int $id): ConditionContract
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int|null $parentId
     *
     * @return ConditionContract
     */
    public function setParentId(?int $parentId): ConditionContract
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * @param bool|null $isInverted
     *
     * @return ConditionContract
     */
    public function setIsInverted(?bool $isInverted): ConditionContract
    {
        $this->isInverted = $isInverted ?? false;

        return $this;
    }

    /**
     * Determines whether this condition result should be inverted.
     *
     * @return bool
     */
    public function isInverted(): bool
    {
        return $this->isInverted;
    }

    /**
     * @param iterable|null $actions
     *
     * @return ConditionContract
     */
    public function setActions(?iterable $actions): ConditionContract
    {
        $this->actions = $actions ?? [];

        return $this;
    }

    /**
     * Adds actions to actions queue.
     *
     * @param ActionContract ...$actions
     */
    protected function addActions(ActionContract ...$actions)
    {
        foreach ($actions as $action) {
            $this->actions[] = $action;
        }
    }

    /**
     * Gets the actions for the condition.
     *
     * @return iterable|ActionContract[]
     */
    public function getActions(): iterable
    {
        return $this->actions;
    }

    /**
     * @param array $parameters
     *
     * @return ConditionContract
     */
    public function setParameters(?array $parameters): ConditionContract
    {
        $this->parameters = $parameters ?? [];

        return $this;
    }

    protected function expectedResult(): bool
    {
        return !$this->isInverted();
    }
}
