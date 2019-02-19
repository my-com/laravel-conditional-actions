<?php

namespace Tests\Entities\Conditions;

use ConditionalActions\Entities\Conditions\OneOfCondition;
use Tests\Dummy\DummyAction;
use Tests\Dummy\DummyFailedCondition;
use Tests\Dummy\DummySucceedCondition;
use Tests\Dummy\DummyTarget;
use Tests\TestCase;

class OneOfConditionTest extends TestCase
{
    /** @var OneOfCondition */
    private $oneOfCondition;

    /** @var DummyTarget */
    private $target;

    /** @var DummyAction */
    private $action;

    protected function setUp()
    {
        parent::setUp();

        $this->target = new DummyTarget();
        $this->action = new DummyAction();

        $this->oneOfCondition = new OneOfCondition();
        $this->oneOfCondition->setId(5);
        $this->oneOfCondition->setActions([$this->action]);

        $this->target->addConditions($this->oneOfCondition);
    }

    public function test_true_when_has_succeed_conditions()
    {
        $actions = [new DummyAction(), new DummyAction(), new DummyAction()];
        $conditions = [
            DummyFailedCondition::withActions(1, $this->oneOfCondition->getId(), $actions[0]),
            DummySucceedCondition::withActions(2, $this->oneOfCondition->getId(), $actions[1]),
            DummySucceedCondition::withActions(3, $this->oneOfCondition->getId(), $actions[2]),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->oneOfCondition->check($this->target, $this->target->getState());

        $this->assertTrue($result);
        $this->assertTrue($conditions[0]->isFired);
        $this->assertTrue($conditions[1]->isFired);
        $this->assertFalse($conditions[2]->isFired);
    }

    public function test_false_when_no_succeeds()
    {
        $actions = [new DummyAction(), new DummyAction()];
        $conditions = [
            DummyFailedCondition::withActions(1, $this->oneOfCondition->getId(), $actions[0]),
            DummyFailedCondition::withActions(2, $this->oneOfCondition->getId(), $actions[1]),
        ];
        $this->target->addConditions(...$conditions);

        $result = $this->oneOfCondition->check($this->target, $this->target->getState());

        $this->assertFalse($result);
        $this->assertTrue($conditions[0]->isFired);
        $this->assertTrue($conditions[1]->isFired);
    }

    public function test_collect_actions_of_first_succeed_condition()
    {
        $actions = [new DummyAction(), new DummyAction(), new DummyAction()];
        $conditions = [
            DummyFailedCondition::withActions(1, $this->oneOfCondition->getId(), $actions[0]),
            DummySucceedCondition::withActions(2, $this->oneOfCondition->getId(), $actions[1]),
            DummySucceedCondition::withActions(3, $this->oneOfCondition->getId(), $actions[2]),
        ];
        $this->target->addConditions(...$conditions);

        $this->oneOfCondition->check($this->target, $this->target->getState());

        $this->assertEquals(
            [$this->action, $actions[1]],
            $this->oneOfCondition->getActions()
        );
    }

    public function test_dont_collect_actions_when_no_succeed_conditions()
    {
        $actions = [new DummyAction(), new DummyAction()];
        $conditions = [
            DummyFailedCondition::withActions(1, $this->oneOfCondition->getId(), $actions[0]),
            DummyFailedCondition::withActions(2, $this->oneOfCondition->getId(), $actions[1]),
        ];
        $this->target->addConditions(...$conditions);

        $this->oneOfCondition->check($this->target, $this->target->getState());

        $this->assertEquals(
            [$this->action],
            $this->oneOfCondition->getActions()
        );
    }
}
