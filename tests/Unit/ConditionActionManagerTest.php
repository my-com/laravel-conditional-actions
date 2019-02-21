<?php

namespace Tests\Unit;

use ConditionalActions\ConditionalActionManager;
use Tests\Helpers\Dummy\Action;
use Tests\Helpers\Dummy\DummyTarget;
use Tests\TestCase;

class ConditionActionManagerTest extends TestCase
{
    /** @var ConditionalActionManager */
    private $manager;

    /** @var DummyTarget */
    private $target;

    protected function setUp()
    {
        parent::setUp();
        $this->manager = \app(ConditionalActionManager::class);
        $this->target = new DummyTarget();
    }

    public function test_run_condition_actions_when_condition_succeed()
    {
        $actions = $this->makeActions(2);
        $condition = $this->succeedCondition(null, ...$actions);
        $this->target->addConditions($condition);

        $this->target->runConditionalActions();

        $this->assertFired($condition);
        $this->assertFired(...$actions);
        $this->assertFired($this->target);
    }

    public function test_not_run_condition_actions_when_condition_failed()
    {
        $actions = $this->makeActions(2);
        $condition = $this->failedCondition(null, ...$actions);
        $this->target->addConditions($condition);

        $this->target->runConditionalActions();

        $this->assertFired($condition);
        $this->assertNotFired(...$actions);
    }

    public function test_run_condition_check_even_if_previous_condition_is_failed()
    {
        $actions = $this->makeActions(2);
        $conditions = [
            $this->failedCondition(null, $actions[0]),
            $this->succeedCondition(null, $actions[1]),
        ];
        $this->target->addConditions(...$conditions);

        $this->target->runConditionalActions();

        $this->assertFired(...$conditions);
        $this->assertNotFired($actions[0]);
        $this->assertFired($actions[1]);
    }

    public function test_inverted_failed_condition_is_succeed_condition()
    {
        $actions = $this->makeActions(2);
        $condition = $this->failedCondition(null, ...$actions);
        $condition->setIsInverted(true);
        $this->target->addConditions($condition);

        $this->target->runConditionalActions();

        $this->assertFired($condition);
        $this->assertFired(...$actions);
    }
}
