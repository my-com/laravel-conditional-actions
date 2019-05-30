<?php

namespace Tests\Feature\Repositories;

use ConditionalActions\Entities\Conditions\TrueCondition;
use ConditionalActions\Entities\Eloquent\Condition;
use ConditionalActions\Exceptions\ConditionNotFoundException;
use ConditionalActions\Repositories\EloquentConditionRepository;
use Tests\EloquentConditionalActionsTestCase;

class EloquentConditionRepositoryTest extends EloquentConditionalActionsTestCase
{
    /** @var EloquentConditionRepository */
    private $conditionsRepository;

    private $validAttributes = [
        'name' => 'TrueCondition',
        'priority' => 5,
        'is_inverted' => true,
        'starts_at' => '2019-01-01 10:00:00',
        'ends_at' => '2019-01-02 20:00:00',
    ];

    protected function setUp()
    {
        parent::setUp();
        $this->conditionsRepository = \app(EloquentConditionRepository::class);
    }

    public function test_store()
    {
        $condition = $this->conditionsRepository->store($this->validAttributes);

        $this->assertIsNumeric($condition->getId());
        $this->assertConditionAttributes($condition);
    }

    public function test_update()
    {
        /** @var Condition $initial */
        $initial = \create(Condition::class, [
            'name' => 'AllOfCondition',
            'priority' => 10,
            'is_inverted' => false,
            'starts_at' => '2018-10-10 00:00:00',
            'ends_at' => '2018-11-10 00:00:00',
        ]);

        $condition = $this->conditionsRepository->update($initial->id, $this->validAttributes);

        $this->assertEquals($initial->id, $condition->getId());
        $this->assertConditionAttributes($condition);
    }

    public function test_update_non_existent()
    {
        $count = Condition::query()->count();

        $this->expectException(ConditionNotFoundException::class);
        $this->expectExceptionMessage('Condition 5 not found');

        $this->conditionsRepository->update(5, $this->validAttributes);

        $this->assertEquals($count, Condition::query()->count());
    }

    public function test_find()
    {
        /** @var Condition $initial */
        $initial = \create(Condition::class, $this->validAttributes);

        $condition = $this->conditionsRepository->find($initial->id);

        $this->assertEquals($initial->id, $condition->getId());
        $this->assertConditionAttributes($condition);
    }

    public function test_destroy()
    {
        /** @var Condition $initial */
        $initial = \create(Condition::class, $this->validAttributes);

        $condition = $this->conditionsRepository->destroy($initial->id);

        /** @var Condition $deleted */
        $deleted = Condition::query()->onlyTrashed()->find($initial->id);
        $this->assertNotNull($deleted);
        $this->assertTrue($deleted->trashed());
        $this->assertEquals($deleted->id, $condition->getId());
        $this->assertConditionAttributes($condition);
    }

    public function test_destroy_non_existent()
    {
        $count = Condition::query()->count();

        $this->expectException(ConditionNotFoundException::class);
        $this->expectExceptionMessage('Condition 5 not found');

        $this->conditionsRepository->destroy(5);

        $this->assertEquals($count, Condition::query()->count());
    }

    private function assertConditionAttributes(\ConditionalActions\Contracts\ConditionContract $condition): void
    {
        $this->assertInstanceOf(TrueCondition::class, $condition);
        $this->assertEquals($this->validAttributes['priority'], $condition->getPriority());
        $this->assertEquals($this->validAttributes['is_inverted'], $condition->isInverted());
        $this->assertEquals($this->validAttributes['starts_at'], $condition->getStartsAt()->toDateTimeString());
        $this->assertEquals($this->validAttributes['ends_at'], $condition->getEndsAt()->toDateTimeString());
    }
}
