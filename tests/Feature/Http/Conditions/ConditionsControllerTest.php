<?php

namespace Tests\Feature\Http\Conditions;

use ConditionalActions\ConditionalActions;
use ConditionalActions\Contracts\Repositories\ConditionRepository;
use ConditionalActions\Entities\Conditions\BaseCondition;
use ConditionalActions\Entities\Conditions\TrueCondition;
use ConditionalActions\Exceptions\ConditionNotFoundException;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Carbon;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ConditionsControllerTest extends TestCase
{
    /** @var Mockery\MockInterface */
    private $conditionRepositoryMock;

    protected function setUp()
    {
        parent::setUp();

        $this->conditionRepositoryMock = Mockery::mock(ConditionRepository::class);
        $this->app->instance(ConditionRepository::class, $this->conditionRepositoryMock);

        ConditionalActions::routes();
    }

    public function test_store_success()
    {
        $params = ['name' => 'TrueCondition'];
        $condition = $this->makeCondition();
        $this->conditionRepositoryMock
            ->shouldReceive('store')
            ->once()
            ->with($params)
            ->andReturn($condition);

        $response = $this->postJson(\route('conditions.store'), $params);

        $this->assertResponse($response, $condition);
    }

    /**
     * @dataProvider provider_store_validation
     *
     * @param array $params
     * @param string ...$errorKeys
     */
    public function test_store_validation(array $params = [], string ...$errorKeys)
    {
        $request = $this->postJson(\route('conditions.store', $params));
        $request->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertValidationErrors($errorKeys, $request);
    }

    public function provider_store_validation(): array
    {
        return [
            'name omitted' => [[], 'name'],
            'unknown name' => [['name' => 'UnknownCondition'], 'name'],
            'is_inverted is not boolean' => [['name' => 'TrueCondition', 'is_inverted' => 'string'], 'is_inverted'],
            'parameters is not array' => [['name' => 'TrueCondition', 'parameters' => 'string'], 'parameters'],
            'priority is not array' => [['name' => 'TrueCondition', 'priority' => 'string'], 'priority'],
            'starts_at is not a date' => [['name' => 'TrueCondition', 'starts_at' => 'string'], 'starts_at'],
            'ends_at is not a date' => [['name' => 'TrueCondition', 'ends_at' => 'string'], 'ends_at'],
        ];
    }

    public function test_update_success()
    {
        $params = ['name' => 'TrueCondition'];
        $condition = $this->makeCondition();
        $this->conditionRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($condition->getId(), $params)
            ->andReturn($condition);

        $response = $this->putJson(\route('conditions.update', $condition->getId()), $params);

        $this->assertResponse($response, $condition);
    }

    public function test_update_non_existent()
    {
        $params = ['name' => 'TrueCondition'];
        $exception = new ConditionNotFoundException('Condition not found');
        $this->conditionRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with(5, $params)
            ->andThrow($exception);

        $response = $this->putJson(\route('conditions.update', 5), $params);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals($exception->getMessage(), $response->json('error.message'));
    }

    public function test_show_success()
    {
        $params = ['name' => 'TrueCondition'];
        $condition = $this->makeCondition();
        $this->conditionRepositoryMock
            ->shouldReceive('find')
            ->once()
            ->with($condition->getId())
            ->andReturn($condition);

        $response = $this->getJson(\route('conditions.show', $condition->getId()), $params);

        $this->assertResponse($response, $condition);
    }

    public function test_destroy_success()
    {
        $condition = $this->makeCondition();
        $this->conditionRepositoryMock
            ->shouldReceive('destroy')
            ->once()
            ->with($condition->getId())
            ->andReturn($condition);

        $response = $this->deleteJson(\route('conditions.destroy', $condition->getId()));

        $this->assertResponse($response, $condition);
    }

    public function test_destroy_non_existent()
    {
        $exception = new ConditionNotFoundException('Condition not found');
        $this->conditionRepositoryMock
            ->shouldReceive('destroy')
            ->once()
            ->with(5)
            ->andThrow($exception);

        $response = $this->deleteJson(\route('conditions.destroy', 5));

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals($exception->getMessage(), $response->json('error.message'));
    }

    /**
     * @dataProvider provider_store_validation
     *
     * @param array $params
     * @param string ...$errorKeys
     */
    public function test_update_validation(array $params = [], string ...$errorKeys)
    {
        $conditionId = 2;
        $request = $this->putJson(\route('conditions.update', $conditionId), $params);
        $request->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertValidationErrors($errorKeys, $request);
    }

    /**
     * @param array $errorKeys
     * @param TestResponse $request
     */
    public function assertValidationErrors(array $errorKeys, TestResponse $request): void
    {
        $this->assertEquals(
            \collect($request->json('errors'))->keys()->sort()->values()->toArray(),
            \collect($errorKeys)->sort()->values()->toArray()
        );
    }

    private function makeCondition(): BaseCondition
    {
        $condition = (new TrueCondition())
            ->setId(3)
            ->setPriority(5)
            ->setIsInverted(true)
            ->setStartsAt(Carbon::yesterday())
            ->setEndsAt(Carbon::tomorrow());

        return $condition;
    }

    private function assertResponse(TestResponse $response, BaseCondition $condition): void
    {
        $response->assertOk();
        $this->assertEquals('TrueCondition', $response->json('data.name'));
        $this->assertEquals($condition->getId(), $response->json('data.id'));
        $this->assertEquals($condition->isInverted(), $response->json('data.is_inverted'));
        $this->assertEquals($condition->getParameters(), $response->json('data.parameters'));
        $this->assertEquals($condition->getPriority(), $response->json('data.priority'));
        $this->assertEquals($condition->getStartsAt()->toIso8601String(), $response->json('data.starts_at'));
        $this->assertEquals($condition->getEndsAt()->toIso8601String(), $response->json('data.ends_at'));
    }
}
