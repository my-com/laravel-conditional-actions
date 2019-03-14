<?php

namespace Tests\Feature\Entities\Eloquent;

use ConditionalActions\Entities\Conditions\TrueCondition;
use ConditionalActions\Entities\Eloquent\Action;
use ConditionalActions\Entities\Eloquent\Condition;
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
        $condition = \create(Condition::class, [
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
        $condition = \create(Condition::class, ['name' => 'NotExists']);

        $this->expectException(ConditionNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Condition NotExists not found');

        $condition->toCondition();
    }

    public function test_to_condition_make_correct_condition()
    {
        /** @var Condition $condition */
        $condition = \create(Condition::class, [
            'name' => 'TrueCondition',
            'is_inverted' => true,
            'parameters' => ['one' => 'first'],
        ]);
        /** @var Action $action */
        $action = \create(Action::class);
        $condition->actions()->save($action);

        $actualCondition = $condition->toCondition();

        $this->assertInstanceOf(TrueCondition::class, $actualCondition);
        $this->assertEquals($condition->id, $actualCondition->getId());
        $this->assertEquals($condition->is_inverted, $actualCondition->isInverted());
        $this->assertEquals($condition->parameters, $actualCondition->getParameters());
        $this->assertEquals([$action->toAction()], $actualCondition->getActions());
    }

    public function test_get_active_actions_filtered_not_active()
    {
        /** @var Condition $condition */
        $condition = \create(Condition::class);
        /** @var Action $action */
        $activeAction = \create(Action::class);
        $inactiveAction = \create(Action::class, ['ends_at' => Carbon::yesterday()]);
        $condition->actions()->saveMany([$activeAction, $inactiveAction]);

        $actions = $condition->getActiveActions();

        $this->assertEquals([$activeAction->id], $actions->pluck('id')->toArray());
    }

    public function test_get_active_actions_sorted_by_priority()
    {
        /** @var Condition $condition */
        $condition = \create(Condition::class);
        [$action10, $action5, $action20] = \createMany(Action::class, ['priority'], [[10], [5], [20]]);
        $condition->actions()->saveMany([$action10, $action5, $action20]);

        $actions = $condition->getActiveActions();

        $this->assertEquals(
            [$action5->id, $action10->id, $action20->id],
            $actions->pluck('id')->toArray()
        );
    }
}
