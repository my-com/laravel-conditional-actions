<?php

namespace Tests\Entities\Conditions;

use ConditionalActions\Entities\Conditions\OneOfCondition;
use Tests\Dummy\DummyAction;

class OneOfConditionTest extends HasChildrenConditionsTestCase
{
    protected function setUp()
    {
        $this->testCondition = new OneOfCondition();
        parent::setUp();
    }

    public function test_true_when_has_succeed_conditions()
    {
        $actions = [new DummyAction(), new DummyAction(), new DummyAction()];
        $conditions = [
            $this->failedChildrenCondition($actions[0]),
            $this->succeedChildrenCondition($actions[1]),
            $this->succeedChildrenCondition($actions[2]),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getState());

        $this->assertTrue($result);
        $this->assertTrue($conditions[0]->isFired);
        $this->assertTrue($conditions[1]->isFired);
        $this->assertFalse($conditions[2]->isFired);
    }

    public function test_false_when_no_succeeds()
    {
        $actions = [new DummyAction(), new DummyAction()];
        $conditions = [
            $this->failedChildrenCondition($actions[0]),
            $this->failedChildrenCondition($actions[1]),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getState());

        $this->assertFalse($result);
        $this->assertTrue($conditions[0]->isFired);
        $this->assertTrue($conditions[1]->isFired);
    }

    public function test_collect_actions_of_first_succeed_condition()
    {
        $actions = [new DummyAction(), new DummyAction(), new DummyAction()];
        $conditions = [
            $this->failedChildrenCondition($actions[0]),
            $this->succeedChildrenCondition($actions[1]),
            $this->succeedChildrenCondition($actions[2]),
        ];
        $this->target->addConditions(...$conditions);

        $this->testCondition->check($this->target, $this->target->getState());

        $this->assertEquals(
            [$this->action, $actions[1]],
            $this->testCondition->getActions()
        );
    }

    public function test_dont_collect_actions_when_no_succeed_conditions()
    {
        $actions = [new DummyAction(), new DummyAction()];
        $conditions = [
            $this->failedChildrenCondition($actions[0]),
            $this->failedChildrenCondition($actions[1]),
        ];
        $this->target->addConditions(...$conditions);

        $this->testCondition->check($this->target, $this->target->getState());

        $this->assertEquals(
            [$this->action],
            $this->testCondition->getActions()
        );
    }
}
