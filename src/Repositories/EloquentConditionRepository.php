<?php

namespace ConditionalActions\Repositories;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Contracts\Repositories\ConditionRepository;
use ConditionalActions\Entities\Eloquent\Condition;
use ConditionalActions\Exceptions\ConditionNotFoundException;

class EloquentConditionRepository implements ConditionRepository
{
    /** @var Condition */
    private $model;

    public function __construct(Condition $model)
    {
        $this->model = $model;
    }

    public function find(int $id): ConditionContract
    {
        return \optional($this->model->newQuery()->find($id))->toCondition();
    }

    public function store(array $attributes): ConditionContract
    {
        return \optional($this->model->newQuery()->create($attributes))->toCondition();
    }

    /**
     * @param int $id
     * @param array $attributes
     *
     * @return ConditionContract
     *
     * @throws ConditionNotFoundException
     * @throws \Throwable
     */
    public function update(int $id, array $attributes): ConditionContract
    {
        /** @var Condition $condition */
        $condition = $this->model->newQuery()->find($id);

        if (!$condition) {
            throw new ConditionNotFoundException(\sprintf('Condition %s not found', $id));
        }

        $condition->update($attributes);

        return $condition->toCondition();
    }

    /**
     * @param int $id
     *
     * @return ConditionContract
     *
     * @throws ConditionNotFoundException
     * @throws \Throwable
     */
    public function destroy(int $id): ConditionContract
    {
        /** @var Condition $condition */
        $condition = $this->model->newQuery()->find($id);

        if (!$condition) {
            throw new ConditionNotFoundException(\sprintf('Condition %s not found', $id));
        }

        $condition->delete();

        return $condition->toCondition();
    }
}
