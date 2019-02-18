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
    protected $isInverted;

    /** @var iterable|null */
    protected $actions;

    /** @var iterable */
    protected $parameters = [];

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

    /**
     * Checks that the condition is met.
     *
     * @param TargetContract $target
     * @param StateContract $state
     *
     * @return bool
     */
    abstract public function check(TargetContract $target, StateContract $state): bool;

    protected function expectedResult(): bool
    {
        return !$this->isInverted();
    }
}
