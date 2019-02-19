<?php

namespace Tests\Dummy;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;
use ConditionalActions\Traits\RunsConditionalActions;
use Illuminate\Support\Collection;

class DummyTarget implements TargetContract
{
    use RunsConditionalActions;

    /** @var StateContract */
    private $state;

    /** @var Collection|ConditionContract[] */
    private $conditions;

    public function __construct()
    {
        $this->conditions = \collect();
    }

    /**
     * Gets state from target.
     *
     * @return StateContract
     */
    public function getState(): StateContract
    {
        return $this->newState([]);
    }

    /**
     * Sets the state to the target.
     *
     * @param StateContract $state
     */
    public function setState(StateContract $state): void
    {
        $this->state = $state;
    }

    /**
     * Gets root target conditions.
     *
     * @return iterable|ConditionContract[]
     */
    public function getRootConditions(): iterable
    {
        return $this->getChildrenConditions(null);
    }

    /**
     * Gets children target conditions.
     *
     * @param int|null $parentId
     *
     * @return iterable|ConditionContract[]
     */
    public function getChildrenConditions(?int $parentId): iterable
    {
        return $this->conditions->filter(function (ConditionContract $condition) use ($parentId) {
            return $condition->getParentId() === $parentId;
        });
    }

    public function addConditions(ConditionContract ...$conditions): self
    {
        foreach ($conditions as $condition) {
            $this->conditions->push($condition);
        }

        return $this;
    }
}
