<?php

namespace Tests\Feature\Http\Conditions;

use ConditionalActions\ConditionalActions;
use ConditionalActions\Contracts\Repositories\ActionRepository;
use ConditionalActions\Entities\Actions\BaseAction;
use ConditionalActions\Entities\Actions\UpdateStateAttributeAction;
use ConditionalActions\Exceptions\ActionNotFoundException;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Support\Carbon;
use Mockery;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ActionsControllerTest extends TestCase
{
    /** @var Mockery\MockInterface */
    private $actionRepositoryMock;

    private $validParams = ['name' => 'UpdateStateAttributeAction', 'condition_id' => 10];

    protected function setUp()
    {
        parent::setUp();

        $this->actionRepositoryMock = Mockery::mock(ActionRepository::class);
        $this->app->instance(ActionRepository::class, $this->actionRepositoryMock);

        ConditionalActions::routes();
    }

    public function test_store_success()
    {
        $action = $this->makeAction();
        $this->actionRepositoryMock
            ->shouldReceive('store')
            ->once()
            ->with($this->validParams)
            ->andReturn($action);

        $response = $this->postJson(\route('actions.store'), $this->validParams);

        $this->assertResponse($response, $action);
    }

    /**
     * @dataProvider provider_store_validation
     *
     * @param array $params
     * @param string ...$errorKeys
     */
    public function test_store_validation(array $params = [], string ...$errorKeys)
    {
        $request = $this->postJson(\route('actions.store', $params));
        $request->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertValidationErrors($errorKeys, $request);
    }

    public function provider_store_validation(): array
    {
        return [
            'name omitted' => [
                ['condition_id' => 10],
                'name',
            ],
            'unknown name' => [
                ['condition_id' => 10, 'name' => 'UnknownAction'],
                'name',
            ],
            'condition_id omitted' => [
                ['name' => 'UpdateStateAttributeAction'],
                'condition_id',
            ],
            'condition_id is not numeric' => [
                ['condition_id' => 'string', 'name' => 'UpdateStateAttributeAction'],
                'condition_id',
            ],
            'parameters is not array' => [
                ['condition_id' => 10, 'name' => 'UpdateStateAttributeAction', 'parameters' => 'string'],
                'parameters',
            ],
            'priority is not array' => [
                ['condition_id' => 10, 'name' => 'UpdateStateAttributeAction', 'priority' => 'string'],
                'priority',
            ],
            'starts_at is not a date' => [
                ['condition_id' => 10, 'name' => 'UpdateStateAttributeAction', 'starts_at' => 'string'],
                'starts_at',
            ],
            'ends_at is not a date' => [
                ['condition_id' => 10, 'name' => 'UpdateStateAttributeAction', 'ends_at' => 'string'],
                'ends_at',
            ],
        ];
    }

    public function test_update_success()
    {
        $action = $this->makeAction();
        $this->actionRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with($action->getId(), $this->validParams)
            ->andReturn($action);

        $response = $this->putJson(\route('actions.update', $action->getId()), $this->validParams);

        $this->assertResponse($response, $action);
    }

    public function test_update_non_existent()
    {
        $exception = new ActionNotFoundException('Action not found');
        $this->actionRepositoryMock
            ->shouldReceive('update')
            ->once()
            ->with(5, $this->validParams)
            ->andThrow($exception);

        $response = $this->putJson(\route('actions.update', 5), $this->validParams);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEquals($exception->getMessage(), $response->json('error.message'));
    }

    public function test_show_success()
    {
        $action = $this->makeAction();
        $this->actionRepositoryMock
            ->shouldReceive('find')
            ->once()
            ->with($action->getId())
            ->andReturn($action);

        $response = $this->getJson(\route('actions.show', $action->getId()), $this->validParams);

        $this->assertResponse($response, $action);
    }

    public function test_destroy_success()
    {
        $action = $this->makeAction();
        $this->actionRepositoryMock
            ->shouldReceive('destroy')
            ->once()
            ->with($action->getId())
            ->andReturn($action);

        $response = $this->deleteJson(\route('actions.destroy', $action->getId()));

        $this->assertResponse($response, $action);
    }

    public function test_destroy_non_existent()
    {
        $exception = new ActionNotFoundException('Action not found');
        $this->actionRepositoryMock
            ->shouldReceive('destroy')
            ->once()
            ->with(5)
            ->andThrow($exception);

        $response = $this->deleteJson(\route('actions.destroy', 5));

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
        $actionId = 2;
        $request = $this->putJson(\route('actions.update', $actionId), $params);
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

    private function makeAction(): BaseAction
    {
        $action = (new UpdateStateAttributeAction())
            ->setId(3)
            ->setPriority(5)
            ->setParameters(['param1' => 'value'])
            ->setStartsAt(Carbon::yesterday())
            ->setEndsAt(Carbon::tomorrow());

        return $action;
    }

    private function assertResponse(TestResponse $response, BaseAction $action): void
    {
        $response->assertOk();
        $this->assertEquals('UpdateStateAttributeAction', $response->json('data.name'));
        $this->assertEquals($action->getId(), $response->json('data.id'));
        $this->assertEquals($action->getParameters(), $response->json('data.parameters'));
        $this->assertEquals($action->getPriority(), $response->json('data.priority'));
        $this->assertEquals($action->getStartsAt()->toIso8601String(), $response->json('data.starts_at'));
        $this->assertEquals($action->getEndsAt()->toIso8601String(), $response->json('data.ends_at'));
    }
}
