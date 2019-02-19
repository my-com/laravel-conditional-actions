<?php

namespace ConditionalActions\Traits;

use ConditionalActions\ConditionActionManager;
use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;
use ConditionalActions\Entities\State;

/**
 * @mixin TargetContract
 */
trait RunsConditionalActions
{
    private $useLogger = false;

    protected function newState(iterable $attributes): StateContract
    {
        $this->useLogger = \config('conditional-actions.use_logger');

        return new State($attributes);
    }

    public function useLogger(): self
    {
        $this->useLogger = true;

        return $this;
    }

    public function runConditionalActions()
    {
        /** @var ConditionActionManager $manager */
        $manager = \app(ConditionActionManager::class);
        $manager->useLogger = $this->useLogger;
        $manager->run($this, $this->getState());
    }
}
