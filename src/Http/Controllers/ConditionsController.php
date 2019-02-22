<?php

namespace ConditionalActions\Http\Controllers;

use ConditionalActions\ConditionalActions;
use ConditionalActions\Contracts\Repositories\ConditionRepository;
use ConditionalActions\Http\Presenters\ConditionPresenter;
use ConditionalActions\Http\Presenters\ResponseWrapper;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;

class ConditionsController
{
    use ValidatesRequests, ResponseWrapper;

    /** @var ConditionRepository */
    private $conditionsRepository;

    /** @var ConditionPresenter */
    private $conditionPresenter;

    public function __construct(
        ConditionRepository $conditionRepository,
        ConditionPresenter $conditionPresenter
    ) {
        $this->conditionsRepository = $conditionRepository;
        $this->conditionPresenter = $conditionPresenter;
    }

    public function show(int $conditionId)
    {
        $condition = $this->conditionsRepository->find($conditionId);

        return $this->jsonResponse($this->conditionPresenter->attributes($condition));
    }

    public function store(Request $request, ConditionalActions $conditionalActions)
    {
        $data = $this->validate($request, [
            'name' => ['required', new In($conditionalActions->getConditionNames())],
            'is_inverted' => 'boolean',
            'parameters' => 'array',
            'priority' => 'integer',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ]);

        $condition = $this->conditionsRepository->store($data);

        return $this->jsonResponse($this->conditionPresenter->attributes($condition));
    }

    public function update(Request $request, ConditionalActions $conditionalActions, int $conditionId)
    {
        $data = $this->validate($request, [
            'name' => ['required', new In($conditionalActions->getConditionNames())],
            'is_inverted' => 'boolean',
            'parameters' => 'array',
            'priority' => 'integer',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ]);

        $condition = $this->conditionsRepository->update($conditionId, $data);

        return $this->jsonResponse($this->conditionPresenter->attributes($condition));
    }

    public function destroy(int $conditionId)
    {
        $condition = $this->conditionsRepository->destroy($conditionId);

        return $this->jsonResponse($this->conditionPresenter->attributes($condition));
    }
}
