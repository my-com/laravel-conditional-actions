<?php

namespace Tests\Entities\Conditions;

use ConditionalActions\Entities\Conditions\AllOfCondition;
use Tests\Dummy\DummyAction;
use Tests\Dummy\DummyCondition;
use Tests\Dummy\DummyFailedCondition;
use Tests\Dummy\DummySucceedCondition;
use Tests\Dummy\DummyTarget;
use Tests\TestCase;

class AllOfConditionTest extends TestCase
{
    /** @var DummyTarget */
    private $target;

    /** @var DummyAction */
    private $action;

    /** @var AllOfCondition */
    private $allOfCondition;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new DummyTarget();
        $this->action = new DummyAction();

        $this->allOfCondition = new AllOfCondition();
        $this->allOfCondition->setId(5);
        $this->allOfCondition->setActions([$this->action]);

        $this->target->addConditions($this->allOfCondition);
    }

    public function test_true_when_all_conditions_is_succeed()
    {
        /** @var DummyCondition[] $conditions */
        $conditions = [
            new DummySucceedCondition(2, 5),
            new DummySucceedCondition(3, 5),
            new DummySucceedCondition(4, 5),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->allOfCondition->check($this->target, $this->target->getState());

        $this->assertTrue($result);
        $this->assertTrue($conditions[0]->isFired);
        $this->assertTrue($conditions[1]->isFired);
        $this->assertTrue($conditions[2]->isFired);
    }

    public function test_false_when_has_failed_condition()
    {
        /** @var DummyCondition[] $conditions */
        $conditions = [
            new DummySucceedCondition(2, 5),
            new DummyFailedCondition(3, 5),
            new DummySucceedCondition(4, 5),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->allOfCondition->check($this->target, $this->target->getState());

        $this->assertFalse($result);

        $this->assertTrue($conditions[0]->isFired);
        $this->assertTrue($conditions[1]->isFired);
        $this->assertFalse($conditions[2]->isFired);
    }

    public function test_child_actions_collected()
    {
        $action1 = new DummyAction();
        /** @var DummyCondition[] $conditions */
        $conditions = [
            new DummySucceedCondition(2, 5),
            DummySucceedCondition::withActions(3, 5, $action1),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->allOfCondition->check($this->target, $this->target->getState());

        $this->assertTrue($result);
        $this->assertEquals([$this->action, $action1], $this->allOfCondition->getActions());
    }

    public function test_child_actions_not_collected_when_failed()
    {
        /** @var DummyCondition[] $conditions */
        $conditions = [
            new DummySucceedCondition(2, 5),
            DummySucceedCondition::withActions(3, 5, new DummyAction()),
            new DummyFailedCondition(4, 5),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->allOfCondition->check($this->target, $this->target->getState());

        $this->assertFalse($result);
        $this->assertEquals([$this->action], $this->allOfCondition->getActions());
    }
}
