<?php

namespace Tests\Feature\Entities\Eloquent;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Entities\Eloquent\Condition;
use ConditionalActions\Entities\Eloquent\ConditionAction;
use ConditionalActions\Exceptions\ConditionNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Tests\EloquentTestCase;

class ConditionTest extends EloquentTestCase
{
    /** @var Carbon */
    private $now = '2019-01-05 10:00:00';

    protected function setUp()
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse($this->now));
    }

    /**
     * @dataProvider provider_is_active
     *
     * @param bool $isActive
     * @param Carbon|null $startsAt
     * @param Carbon|null $endsAt
     */
    public function test_is_active(bool $isActive, $startsAt, $endsAt)
    {
        /** @var Condition $condition */
        $condition = \factory(Condition::class)->create([
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        $this->assertEquals($isActive, $condition->isActive());
    }

    public function provider_is_active(): array
    {
        Carbon::setTestNow(Carbon::parse($this->now));

        return [
            'starts_at at now and ends_at in future' => [true, Carbon::now(), Carbon::tomorrow()],
            'starts_at in past and ends_at at now' => [true, Carbon::yesterday(), Carbon::now()],
            'starts_at in past and ends_at in future' => [true, Carbon::yesterday(), Carbon::tomorrow()],
            'starts_at is null and ends_at at now' => [true, null, Carbon::now()],
            'starts_at at now and ends_at is null' => [true, Carbon::now(), null],
            'starts_at is null and ends_at is null' => [true, null, null],
            'starts_at in past and ends_at in past' => [false, Carbon::yesterday()->subDay(), Carbon::yesterday()],
            'starts_at is null and ends_at in past' => [false, null, Carbon::yesterday()],
            'starts_at in future and ends_at in future' => [false, Carbon::tomorrow(), Carbon::tomorrow()->addDay()],
            'starts_at in future and ends_at is null' => [false, Carbon::tomorrow(), null],
        ];
    }

    public function test_to_condition_exception_when_condition_not_exists()
    {
        /** @var Condition $condition */
        $condition = \factory(Condition::class)->create(['name' => 'NotExists']);

        $this->expectException(ConditionNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Condition Exists not found');

        $condition->toCondition();
    }

    public function test_to_response_make_correct_condition()
    {
        /** @var Condition $condition */
        $condition = \factory(Condition::class)->create([
            'name' => 'True',
            'is_inverted' => true,
            'parameters' => ['one' => 'first'],
        ]);
        /** @var ConditionAction $action */
        $action = \factory(ConditionAction::class)->create();
        $condition->conditionActions()->save($action);

        $actualCondition = $condition->toCondition();

        $this->assertInstanceOf(ConditionContract::class, $actualCondition);
        $this->assertEquals($condition->id, $actualCondition->getId());
        $this->assertEquals($condition->is_inverted, $actualCondition->isInverted());
        $this->assertEquals($condition->parameters, $actualCondition->getParameters());
        $this->assertEquals([$action->toAction()], \iterator_to_array($actualCondition->getActions()));
    }

    public function test_get_active_actions_filters_not_active()
    {
        /** @var Condition $condition */
        $condition = \factory(Condition::class)->create();
        /** @var ConditionAction $action */
        $activeAction = \factory(ConditionAction::class)->create();
        $inactiveAction = \factory(ConditionAction::class)->create(['ends_at' => Carbon::yesterday()]);
        $condition->conditionActions()->saveMany([$activeAction, $inactiveAction]);

        $actions = $condition->getActiveActions();

        $this->assertEquals([$activeAction->id], $actions->pluck('id')->toArray());
    }

    public function test_get_active_actions_sort_by_priority()
    {
        /** @var Condition $condition */
        $condition = \factory(Condition::class)->create();
        /** @var ConditionAction $action */
        $action10 = \factory(ConditionAction::class)->create(['priority' => 10]);
        $action5 = \factory(ConditionAction::class)->create(['priority' => 5]);
        $action20 = \factory(ConditionAction::class)->create(['priority' => 20]);
        $condition->conditionActions()->saveMany([$action10, $action5, $action20]);

        $actions = $condition->getActiveActions();

        $this->assertEquals(
            [$action5->id, $action10->id, $action20->id],
            $actions->pluck('id')->toArray()
        );
    }
}
