<?php

namespace Tests\Unit\Entities\Conditions;

use ConditionalActions\Entities\Conditions\AllOfCondition;
use Tests\Helpers\Dummy\Action;
use Tests\Helpers\Dummy\DummyCondition;

class AllOfConditionTest extends HasChildrenConditionsTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->makeTestCondition(\app(AllOfCondition::class));
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

        $result = $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertTrue($result);
        $this->assertFired(...$conditions);
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

        $result = $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertFalse($result);

        $this->assertFired($conditions[0]);
        $this->assertFired($conditions[1]);
        $this->assertNotFired($conditions[2]);
    }

    public function test_child_actions_collected()
    {
        $action1 = new Action();
        /** @var DummyCondition[] $conditions */
        $conditions = [
            $this->succeedChildrenCondition(),
            $this->succeedChildrenCondition($action1),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertTrue($result);
        $this->assertEquals([$this->action, $action1], $this->testCondition->getActions());
    }

    public function test_child_actions_not_collected_when_failed()
    {
        /** @var DummyCondition[] $conditions */
        $conditions = [
            $this->succeedChildrenCondition(),
            $this->succeedChildrenCondition(new Action()),
            $this->failedChildrenCondition(new Action()),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertFalse($result);
        $this->assertEquals([$this->action], $this->testCondition->getActions());
    }

    public function test_inverted_failed_condition_succeed()
    {
        $condition = $this->failedChildrenCondition();
        $condition->setIsInverted(true);
        $this->target->addConditions($condition);

        $result = $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertFalse($result);
        $this->assertFired($condition);
    }
}
