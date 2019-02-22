<?php

namespace ConditionalActions\Http\Controllers;

use ConditionalActions\ConditionalActions;
use ConditionalActions\Contracts\Repositories\ActionRepository;
use ConditionalActions\Http\Presenters\ActionPresenter;
use ConditionalActions\Http\Presenters\ResponseWrapper;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\In;

class ActionsController
{
    use ValidatesRequests, ResponseWrapper;

    /** @var ActionRepository */
    private $actionRepository;

    /** @var ActionPresenter */
    private $actionPresenter;

    public function __construct(
        ActionRepository $actionRepository,
        ActionPresenter $actionPresenter
    ) {
        $this->actionRepository = $actionRepository;
        $this->actionPresenter = $actionPresenter;
    }

    public function show(int $actionId)
    {
        $action = $this->actionRepository->find($actionId);

        return $this->jsonResponse($this->actionPresenter->attributes($action));
    }

    public function store(Request $request, ConditionalActions $conditionalActions)
    {
        $data = $this->validate($request, [
            'name' => ['required', new In($conditionalActions->getActionNames())],
            'condition_id' => 'required|numeric',
            'parameters' => 'array',
            'priority' => 'integer',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ]);

        $action = $this->actionRepository->store($data);

        return $this->jsonResponse($this->actionPresenter->attributes($action));
    }

    public function update(Request $request, ConditionalActions $conditionalActions, int $actionId)
    {
        $data = $this->validate($request, [
            'name' => ['required', new In($conditionalActions->getActionNames())],
            'condition_id' => 'required|numeric',
            'parameters' => 'array',
            'priority' => 'integer',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ]);

        $action = $this->actionRepository->update($actionId, $data);

        return $this->jsonResponse($this->actionPresenter->attributes($action));
    }

    public function destroy(int $actionId)
    {
        $condition = $this->actionRepository->destroy($actionId);

        return $this->jsonResponse($this->actionPresenter->attributes($condition));
    }
}
