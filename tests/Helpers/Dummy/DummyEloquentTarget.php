<?php

namespace Tests\Helpers\Dummy;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;
use ConditionalActions\Traits\RunsConditionalActions;

class DummyEloquentTarget implements TargetContract
{
    use RunsConditionalActions;

    /** @var DummyEloquentModel */
    private $model;

    public $state;

    public function __construct(DummyEloquentModel $model)
    {
        $this->model = $model;
    }

    /**
     * Gets state from target.
     *
     * @return StateContract
     */
    public function getInitialState(): StateContract
    {
        return $this->newState(['value' => 1]);
    }

    /**
     * Sets the state to the target.
     *
     * @param StateContract $state
     */
    public function applyState(StateContract $state): void
    {
        $this->state = $state->toArray();
    }

    /**
     * Gets root target conditions.
     *
     * @return iterable|ConditionContract[]
     */
    public function getRootConditions(): iterable
    {
        return $this->model->getRootConditions();
    }

    /**
     * Gets children target conditions.
     *
     * @param int $parentId
     *
     * @return iterable|ConditionContract[]
     */
    public function getChildrenConditions(int $parentId): iterable
    {
        return $this->model->getChildrenConditions($parentId);
    }
}
