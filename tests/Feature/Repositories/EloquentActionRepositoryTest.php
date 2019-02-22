<?php

namespace Tests\Feature\Repositories;

use ConditionalActions\Contracts\ActionContract;
use ConditionalActions\Entities\Actions\UpdateStateAttributeAction;
use ConditionalActions\Entities\Eloquent\Action;
use ConditionalActions\Entities\Eloquent\Condition;
use ConditionalActions\Exceptions\ActionNotFoundException;
use ConditionalActions\Repositories\EloquentActionRepository;
use Tests\EloquentTestCase;

class EloquentActionRepositoryTest extends EloquentTestCase
{
    /** @var EloquentActionRepository */
    private $actionsRepository;

    private $validAttributes = [
        'name' => 'UpdateStateAttributeAction',
        'priority' => 5,
        'parameters' => ['param' => 'value'],
        'starts_at' => '2019-01-01 10:00:00',
        'ends_at' => '2019-01-02 20:00:00',
    ];

    protected function setUp()
    {
        parent::setUp();

        $this->actionsRepository = \app(EloquentActionRepository::class);
        /** @var Condition $condition */
        $condition = Condition::query()->create(['name' => 'TrueCondition']);
        $this->validAttributes['condition_id'] = $condition->id;
    }

    public function test_store()
    {
        $condition = $this->actionsRepository->store($this->validAttributes);

        $this->assertIsNumeric($condition->getId());
        $this->assertConditionAttributes($condition);
    }

    public function test_update()
    {
        /** @var Condition $initial */
        $initial = \create(Action::class, [
            'name' => 'UpdateStateAttributeAction',
            'priority' => 10,
            'condition_id' => $this->validAttributes['condition_id'],
            'starts_at' => '2018-10-10 00:00:00',
            'ends_at' => '2018-11-10 00:00:00',
        ]);

        $condition = $this->actionsRepository->update($initial->id, $this->validAttributes);

        $this->assertEquals($initial->id, $condition->getId());
        $this->assertConditionAttributes($condition);
    }

    public function test_update_non_existent()
    {
        $count = Action::query()->count();

        $this->expectException(ActionNotFoundException::class);
        $this->expectExceptionMessage('Action 5 not found');

        $this->actionsRepository->update(5, $this->validAttributes);

        $this->assertEquals($count, Action::query()->count());
    }

    public function test_find()
    {
        /** @var Action $initial */
        $initial = \create(Action::class, $this->validAttributes);

        $action = $this->actionsRepository->find($initial->id);

        $this->assertEquals($initial->id, $action->getId());
        $this->assertConditionAttributes($action);
    }

    public function test_destroy()
    {
        /** @var Condition $initial */
        $initial = \create(Action::class, $this->validAttributes);

        $action = $this->actionsRepository->destroy($initial->id);

        /** @var Condition $deleted */
        $deleted = Action::query()->onlyTrashed()->find($initial->id);
        $this->assertNotNull($deleted);
        $this->assertTrue($deleted->trashed());
        $this->assertEquals($deleted->id, $action->getId());
        $this->assertConditionAttributes($action);
    }

    public function test_destroy_non_existent()
    {
        $count = Action::query()->count();

        $this->expectException(ActionNotFoundException::class);
        $this->expectExceptionMessage('Action 5 not found');

        $this->actionsRepository->destroy(5);

        $this->assertEquals($count, Action::query()->count());
    }

    private function assertConditionAttributes(ActionContract $condition): void
    {
        $this->assertInstanceOf(UpdateStateAttributeAction::class, $condition);
        $this->assertEquals($this->validAttributes['priority'], $condition->getPriority());
        $this->assertEquals($this->validAttributes['parameters'], $condition->getParameters());
        $this->assertEquals($this->validAttributes['starts_at'], $condition->getStartsAt()->toDateTimeString());
        $this->assertEquals($this->validAttributes['ends_at'], $condition->getEndsAt()->toDateTimeString());
    }
}
