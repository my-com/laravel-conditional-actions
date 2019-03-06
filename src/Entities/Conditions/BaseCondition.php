<?php

namespace ConditionalActions\Entities\Conditions;

use ConditionalActions\Contracts\ActionContract;
use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;
use Illuminate\Support\Carbon;

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

    /** @var int */
    protected $priority = 0;

    /** @var Carbon|null */
    protected $startsAt;

    /** @var Carbon|null */
    protected $endsAt;

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
     * Sets the priority.
     *
     * @param int $priority
     *
     * @return BaseCondition
     */
    public function setPriority(int $priority): BaseCondition
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Gets priority.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Sets the starts time.
     *
     * @param Carbon|null $startsAt
     *
     * @return BaseCondition
     */
    public function setStartsAt(?Carbon $startsAt): BaseCondition
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    /**
     * Gets starts time.
     *
     * @return Carbon|null
     */
    public function getStartsAt(): ?Carbon
    {
        return $this->startsAt;
    }

    /**
     * Sets the finishes time.
     *
     * @param Carbon|null $endsAt
     *
     * @return BaseCondition
     */
    public function setEndsAt(?Carbon $endsAt): BaseCondition
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    /**
     * Gets finishes time.
     *
     * @return Carbon|null
     */
    public function getEndsAt(): ?Carbon
    {
        return $this->endsAt;
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
     * @return iterable|ActionContract[]
     */
    public function getActions(): iterable
    {
        return $this->actions;
    }

    /**
     * Adds actions to actions queue.
     *
     * @param ActionContract ...$actions
     */
    protected function addActions(ActionContract ...$actions)
    {
        $this->actions = \array_merge(
            (array) $this->actions, $actions
        );
    }

    /**
     * Adds actions to actions queue.
     *
     * @param ActionContract ...$actions
     */
    protected function prependActions(ActionContract ...$actions)
    {
        $this->actions = \array_merge(
            $actions, (array) $this->actions
        );
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

    public function toArray()
    {
        return [
            'id' => $this
        ];
    }

    protected function expectedResult(): bool
    {
        return !$this->isInverted();
    }
}
