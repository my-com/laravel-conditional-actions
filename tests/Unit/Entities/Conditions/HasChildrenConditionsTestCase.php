<?php

namespace Tests\Unit\Entities\Conditions;

use ConditionalActions\Contracts\ActionContract;
use ConditionalActions\Contracts\ConditionContract;
use Tests\Helpers\Dummy\DummyAction;
use Tests\Helpers\Dummy\DummyCondition;
use Tests\Helpers\Dummy\DummyTarget;
use Tests\TestCase;

class HasChildrenConditionsTestCase extends TestCase
{
    /** @var ConditionContract */
    protected $testCondition;

    /** @var DummyTarget */
    protected $target;

    /** @var DummyAction */
    protected $action;

    protected function makeTestCondition(ConditionContract $condition)
    {
        $this->target = new DummyTarget();
        $this->action = new DummyAction();

        $this->testCondition = $condition;
        $this->testCondition->setId(++$this->id);
        $this->testCondition->setActions([$this->action]);

        $this->target->addConditions($this->testCondition);
    }

    protected function succeedChildrenCondition(ActionContract ...$actions): DummyCondition
    {
        return $this->succeedCondition($this->testCondition->getId(), ...$actions);
    }

    protected function failedChildrenCondition(ActionContract ...$actions): DummyCondition
    {
        return $this->failedCondition($this->testCondition->getId(), ...$actions);
    }
}
