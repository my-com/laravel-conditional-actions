<?php

namespace Tests\Feature;

use ConditionalActions\Entities\Eloquent\Action;
use ConditionalActions\Entities\Eloquent\Condition;
use Tests\EloquentTestCase;
use Tests\Helpers\Dummy\DummyEloquentModel;
use Tests\Helpers\Dummy\DummyEloquentTarget;

class ConditionalActionManagerTest extends EloquentTestCase
{
    public function test_run_conditional_actions_succeeded()
    {
        /** @var DummyEloquentModel $model */
        $model = \create(DummyEloquentModel::class);
        /** @var Condition $allOf */
        $allOf = \create(Condition::class, ['name' => 'AllOfCondition']);
        /** @var Condition $child */
        $child = \create(Condition::class, ['name' => 'TrueCondition', 'parent_id' => $allOf->id]);

        \create(Action::class, [
            'name' => 'UpdateStateAttributeAction',
            'condition_id' => $allOf->id,
            'parameters' => ['value' => 10],
        ]);

        \create(Action::class, [
            'name' => 'UpdateStateAttributeAction',
            'condition_id' => $allOf->id,
            'parameters' => ['value' => 20],
        ]);

        $model->conditions()->saveMany([$allOf, $child]);

        $target = new DummyEloquentTarget($model);

        $state = $target->runConditionalActions();

        $this->assertEquals(20, $state->getAttribute('value'));
        $this->assertEquals(['value' => 20], $target->state);
    }
}
