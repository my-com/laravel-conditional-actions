<?php

namespace ConditionalActions\Repositories;

use ConditionalActions\Contracts\ActionContract;
use ConditionalActions\Contracts\Repositories\ActionRepository;
use ConditionalActions\Entities\Eloquent\Action;
use ConditionalActions\Exceptions\ActionNotFoundException;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class EloquentActionRepository implements ActionRepository
{
    /** @var Action */
    private $model;

    public function __construct(Action $model)
    {
        $this->model = $model;
    }

    public function find(int $id): ActionContract
    {
        return \optional($this->model->newQuery()->find($id))->toAction();
    }

    public function store(array $attributes): ActionContract
    {
        return \optional($this->model->newQuery()->create($attributes))->toAction();
    }

    /**
     * @param int $id
     * @param array $attributes
     *
     * @throws ActionNotFoundException
     * @throws \Throwable
     *
     * @return ActionContract
     */
    public function update(int $id, array $attributes): ActionContract
    {
        /** @var Action $condition */
        $condition = $this->model->newQuery()->find($id);

        if (!$condition) {
            throw new ActionNotFoundException(\sprintf('Action %s not found', $id));
        }

        $condition->update($attributes);

        return $condition->toAction();
    }

    /**
     * @param int $id
     *
     * @throws ActionNotFoundException
     * @throws \Throwable
     *
     * @return ActionContract
     */
    public function destroy(int $id): ActionContract
    {
        /** @var Action $condition */
        $condition = $this->model->newQuery()->find($id);

        if (!$condition) {
            throw new ActionNotFoundException(\sprintf('Action %s not found', $id));
        }

        $condition->delete();

        return $condition->toAction();
    }
}
