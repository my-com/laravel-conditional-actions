<?php

namespace Tests\Feature\Entities\Eloquent;

use ConditionalActions\Entities\Actions\UpdateStateAttributeAction;
use ConditionalActions\Entities\Eloquent\Action;
use ConditionalActions\Exceptions\ActionNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Tests\EloquentConditionalActionsTestCase;

class ConditionActionTest extends EloquentConditionalActionsTestCase
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
        /** @var Action $action */
        $action = \create(Action::class, [
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
        ]);

        $this->assertEquals($isActive, $action->isActive());
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

    public function test_to_action_exception_when_action_not_exists()
    {
        /** @var Action $action */
        $action = \create(Action::class, ['name' => 'NotExists']);

        $this->expectException(ActionNotFoundException::class);
        $this->expectExceptionCode(Response::HTTP_NOT_FOUND);
        $this->expectExceptionMessage('Action NotExists not found');

        $action->toAction();
    }

    public function test_to_action_make_correct_action()
    {
        /** @var Action $action */
        $action = \create(Action::class, [
            'name' => 'UpdateStateAttributeAction',
            'parameters' => ['one' => 'first'],
        ]);

        $actualAction = $action->toAction();

        $this->assertInstanceOf(UpdateStateAttributeAction::class, $actualAction);
        $this->assertEquals($action->parameters, $actualAction->getParameters());
    }

    public function test_validate_name()
    {
        $this->expectException(ValidationException::class);
        \create(Action::class, ['name' => '']);
    }
}
