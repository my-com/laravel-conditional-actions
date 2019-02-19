<?php

namespace Tests\Entities\Conditions;

use ConditionalActions\Contracts\ActionContract;
use ConditionalActions\Contracts\ConditionContract;
use Tests\Dummy\DummyAction;
use Tests\Dummy\DummyCondition;
use Tests\Dummy\DummyFailedCondition;
use Tests\Dummy\DummySucceedCondition;
use Tests\Dummy\DummyTarget;
use Tests\TestCase;

class HasChildrenConditionsTestCase extends TestCase
{
    /** @var ConditionContract */
    protected $testCondition;

    /** @var DummyTarget */
    protected $target;

    /** @var DummyAction */
    protected $action;

    private $id = 5;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new DummyTarget();
        $this->action = new DummyAction();

        $this->testCondition->setId(++$this->id);
        $this->testCondition->setActions([$this->action]);

        $this->target->addConditions($this->testCondition);
    }
    protected function succeedChildrenCondition(ActionContract ...$actions): DummyCondition
    {
        return DummySucceedCondition::withActions(++$this->id, $this->testCondition->getId(), ...$actions);
    }

    protected function failedChildrenCondition(ActionContract ...$actions): DummyCondition
    {
        return DummyFailedCondition::withActions(++$this->id, $this->testCondition->getId(), ...$actions);
    }
}
