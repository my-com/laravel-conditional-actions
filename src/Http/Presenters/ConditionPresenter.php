<?php

namespace ConditionalActions\Http\Presenters;

use ConditionalActions\ConditionalActions;
use ConditionalActions\Contracts\ConditionContract;

class ConditionPresenter extends Presenter
{
    /** @var ConditionalActions */
    private $conditionalActions;

    public function __construct(ConditionalActions $conditionalActions)
    {
        $this->conditionalActions = $conditionalActions;
    }

    public function attributes(?ConditionContract $condition): array
    {
        return $condition ? [
            'id' => $condition->getId(),
            'name' => $this->conditionalActions->getConditionName(\get_class($condition)),
            'is_inverted' => $condition->isInverted(),
            'parameters' => $condition->getParameters(),
            'priority' => $condition->getPriority(),
            'starts_at' => $condition->getStartsAt(),
            'ends_at' => $condition->getEndsAt(),
        ] : null;
    }
}
