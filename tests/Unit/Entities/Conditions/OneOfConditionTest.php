<?php

namespace Tests\Unit\Entities\Conditions;

use ConditionalActions\Entities\Conditions\OneOfCondition;
use Tests\Helpers\Dummy\Action;

class OneOfConditionTest extends HasChildrenConditionsTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->makeTestCondition(\app(OneOfCondition::class));
    }

    public function test_true_when_has_succeed_conditions()
    {
        $conditions = [
            $this->failedChildrenCondition(new Action()),
            $this->succeedChildrenCondition(new Action()),
            $this->succeedChildrenCondition(new Action()),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertTrue($result);
        $this->assertFired($conditions[0]);
        $this->assertFired($conditions[1]);
        $this->assertNotFired($conditions[2]);
    }

    public function test_false_when_no_succeeds()
    {
        $conditions = [
            $this->failedChildrenCondition(new Action()),
            $this->failedChildrenCondition(new Action()),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertFalse($result);
        $this->assertFired(...$conditions);
    }

    public function test_collect_actions_of_first_succeed_condition()
    {
        $actions = $this->makeActions(3);
        $conditions = [
            $this->failedChildrenCondition($actions[0]),
            $this->succeedChildrenCondition($actions[1]),
            $this->succeedChildrenCondition($actions[2]),
        ];
        $this->target->addConditions(...$conditions);

        $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertEquals(
            [$this->action, $actions[1]],
            $this->testCondition->getActions()
        );
    }

    public function test_dont_collect_actions_when_no_succeed_conditions()
    {
        $conditions = [
            $this->failedChildrenCondition(new Action()),
            $this->failedChildrenCondition(new Action()),
        ];
        $this->target->addConditions(...$conditions);

        $this->testCondition->check($this->target, $this->target->getInitialState());

        $this->assertEquals(
            [$this->action],
            $this->testCondition->getActions()
        );
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
