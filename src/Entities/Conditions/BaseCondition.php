<?php

namespace ConditionalActions\Entities\Conditions;

use ConditionalActions\Contracts\ConditionActionContract;
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
     * Runs condition check.
     *
     * @param TargetContract $target
     * @param StateContract $state
     *
     * @return bool
     */
    abstract public function check(TargetContract $target, StateContract $state): bool;

    /**
     * Sets condition identifier.
     *
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
     * Gets condition identifier.
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets parent identifier.
     *
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
     * Gets parent identifier.
     *
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * Sets inverted flag.
     *
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
     * Sets actions.
     *
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
     * Gets the condition actions.
     *
     * @return iterable|ConditionActionContract[]
     */
    public function getActions(): iterable
    {
        return $this->actions;
    }

    /**
     * Adds actions to actions queue.
     *
     * @param ConditionActionContract ...$actions
     */
    protected function addActions(ConditionActionContract ...$actions)
    {
        foreach ($actions as $action) {
            $this->actions[] = $action;
        }
    }

    /**
     * Sets the condition parameters.
     *
     * @param array $parameters
     *
     * @return ConditionContract
     */
    public function setParameters(?array $parameters): ConditionContract
    {
        $this->parameters = $parameters ?? [];

        return $this;
    }

    /**
     * Gets condition parameters.
     *
     * @return iterable
     */
    public function getParameters(): iterable
    {
        return $this->parameters;
    }

    protected function expectedResult(): bool
    {
        return !$this->isInverted();
    }
}
