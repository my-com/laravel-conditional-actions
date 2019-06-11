<?php

namespace Tests\Unit\Entities\Conditions;

use ConditionalActions\ConditionalActionException;
use ConditionalActions\Entities\Conditions\ValidationCondition;
use Tests\ConditionalActionsTestCase;
use Tests\Helpers\Dummy\DummyTarget;
use Tests\Helpers\Dummy\DummyValidationDataTarget;

class ValidationConditionTest extends ConditionalActionsTestCase
{
    /** @var ValidationCondition */
    private $condition;

    protected function setUp(): void
    {
        parent::setUp();
        $this->condition = new ValidationCondition();
    }

    /**
     * @param array $validationData
     * @param array $conditionParams
     * @param bool $result
     *
     * @dataProvider provider_test_validation
     * @throws ConditionalActionException
     */
    public function test_validation(array $validationData, array $conditionParams, bool $result)
    {
        $this->condition->setParameters($conditionParams);
        $target = new DummyValidationDataTarget($validationData);

        $this->assertEquals($result, $this->condition->check($target, $target->getInitialState()));
    }

    public function provider_test_validation(): array
    {
        return [
            'succeeded' => [
                ['foo' => ['bar' => 10]],
                ['foo.bar' => 'required|int|max:9'],
                false,
            ],
            'failed' => [
                ['foo' => ['bar' => 10]],
                ['foo.bar' => 'required|int|max:11'],
                true,
            ],
        ];
    }

    /**
     * @throws ConditionalActionException
     */
    public function test_exception_when_provides_validation_data_contract_not_implemented()
    {
        $target = new DummyTarget();

        $this->expectException(ConditionalActionException::class);
        $this->expectExceptionCode(400);

        $this->condition->check($target, $target->getInitialState());
    }
}
