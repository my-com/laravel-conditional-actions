<?php

namespace Tests\Helpers\Dummy;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;
use ConditionalActions\Traits\RunsConditionalActions;
use Illuminate\Support\Collection;

class DummyTarget implements TargetContract, CanBeFired
{
    use RunsConditionalActions;

    /** @var StateContract */
    private $state;

    /** @var Collection|ConditionContract[] */
    private $conditions;

    private $isFired = false;

    public function __construct()
    {
        $this->conditions = \collect();
    }

    /**
     * Gets state from target.
     *
     * @return StateContract
     */
    public function getInitialState(): StateContract
    {
        return $this->newState([]);
    }

    /**
     * Sets the state to the target.
     *
     * @param StateContract $state
     */
    public function applyState(StateContract $state): void
    {
        $this->isFired = true;
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

    public function isFired(): bool
    {
        return $this->isFired;
    }
}
