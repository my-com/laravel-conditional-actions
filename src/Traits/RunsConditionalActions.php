<?php

namespace ConditionalActions\Traits;

use ConditionalActions\ConditionalActionManager;
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

    public function runConditionalActions(): StateContract
    {
        /** @var ConditionalActionManager $manager */
        $manager = \app(ConditionalActionManager::class);
        $manager->useLogger = $this->useLogger;

        return $manager->run($this, $this->getInitialState());
    }
}
