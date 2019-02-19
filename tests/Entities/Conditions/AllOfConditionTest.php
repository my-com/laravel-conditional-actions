<?php

namespace Tests\Entities\Conditions;

use ConditionalActions\Entities\Conditions\AllOfCondition;
use Tests\Dummy\DummyAction;
use Tests\Dummy\DummyCondition;

class AllOfConditionTest extends HasChildrenConditionsTestCase
{
    protected function setUp()
    {
        $this->testCondition = new AllOfCondition();
        parent::setUp();
    }

    public function test_true_when_all_conditions_is_succeed()
    {
        /** @var DummyCondition[] $conditions */
        $conditions = [
            $this->succeedChildrenCondition(),
            $this->succeedChildrenCondition(),
            $this->succeedChildrenCondition(),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getState());

        $this->assertTrue($result);
        $this->assertTrue($conditions[0]->isFired);
        $this->assertTrue($conditions[1]->isFired);
        $this->assertTrue($conditions[2]->isFired);
    }

    public function test_false_when_has_failed_condition()
    {
        /** @var DummyCondition[] $conditions */
        $conditions = [
            $this->succeedChildrenCondition(),
            $this->failedChildrenCondition(),
            $this->succeedChildrenCondition(),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getState());

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
            $this->succeedChildrenCondition(),
            $this->succeedChildrenCondition($action1),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getState());

        $this->assertTrue($result);
        $this->assertEquals([$this->action, $action1], $this->testCondition->getActions());
    }

    public function test_child_actions_not_collected_when_failed()
    {
        /** @var DummyCondition[] $conditions */
        $conditions = [
            $this->succeedChildrenCondition(),
            $this->succeedChildrenCondition(new DummyAction()),
            $this->failedChildrenCondition(new DummyAction()),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getState());

        $this->assertFalse($result);
        $this->assertEquals([$this->action], $this->testCondition->getActions());
    }

    public function test_inverted_failed_condition_succeed()
    {
        $condition = $this->failedChildrenCondition();
        $condition->setIsInverted(true);
        $this->target->addConditions($condition);

        $result = $this->testCondition->check($this->target, $this->target->getState());

        $this->assertFalse($result);
        $this->assertTrue($condition->isFired);
    }
}
