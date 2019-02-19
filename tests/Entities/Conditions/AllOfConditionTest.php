<?php

namespace Tests\Entities\Conditions;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Entities\Conditions\AllOfCondition;
use Illuminate\Support\Collection;
use Tests\Dummy\DummyAction;
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

    public function test_all_true()
    {
        $conditions = collect([
            new DummySucceedCondition(2, 5),
            new DummySucceedCondition(3, 5),
            new DummySucceedCondition(4, 5),
        ]);
        $this->target->addConditions(...$conditions);

        $this->target->runConditionalActions();

        $this->assertTrue($this->action->isFired);
        $this->assertTrue($conditions->every('isFired', true));
    }

    public function test_not_all_is_true()
    {
        $conditions = collect([
            new DummySucceedCondition(2, 5),
            new DummyFailedCondition(3, 5),
            new DummySucceedCondition(4, 5),
        ]);
        $this->target->addConditions(...$conditions);

        $this->target->runConditionalActions();

        $this->assertFalse($this->action->isFired);

        $this->assertTrue($conditions[0]->isFired);
        $this->assertTrue($conditions[1]->isFired);
        $this->assertFalse($conditions[2]->isFired);
    }

    public function test_child_actions_collected()
    {
        /** @var Collection|ConditionContract[] $conditions */
        $conditions = collect([
            new DummySucceedCondition(2, 5),
            new DummySucceedCondition(3, 5),
        ]);
        $this->target->addConditions(...$conditions);
        $action1 = new DummyAction();
        $conditions[1]->setActions([$action1]);

        $result = $this->allOfCondition->check($this->target, $this->target->getState());

        $this->assertTrue($result);
        $this->assertEquals([$this->action, $action1], $this->allOfCondition->getActions());
    }

    public function test_child_actions_not_collected_when_failed()
    {
        /** @var Collection|ConditionContract[] $conditions */
        $conditions = collect([
            new DummySucceedCondition(2, 5),
            new DummySucceedCondition(3, 5),
            new DummyFailedCondition(4, 5),
        ]);
        $this->target->addConditions(...$conditions);
        $conditions[1]->setActions([new DummyAction()]);

        $result = $this->allOfCondition->check($this->target, $this->target->getState());

        $this->assertFalse($result);
        $this->assertEquals([$this->action], $this->allOfCondition->getActions());
    }
}
