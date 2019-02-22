<?php

namespace ConditionalActions\Http\Presenters;

use ConditionalActions\ConditionalActions;
use ConditionalActions\Contracts\ActionContract;

class ActionPresenter extends Presenter
{
    /** @var ConditionalActions */
    private $conditionalActions;

    public function __construct(ConditionalActions $conditionalActions)
    {
        $this->conditionalActions = $conditionalActions;
    }

    public function attributes(?ActionContract $action): array
    {
        return $action ? [
            'id' => $action->getId(),
            'name' => $this->conditionalActions->getActionName(\get_class($action)),
            'parameters' => $action->getParameters(),
            'priority' => $action->getPriority(),
            'starts_at' => $action->getStartsAt(),
            'ends_at' => $action->getEndsAt(),
        ] : null;
    }
}
