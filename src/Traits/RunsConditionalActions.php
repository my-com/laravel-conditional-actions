<?php

namespace ConditionalActions\Traits;

use ConditionalActions\ConditionActionManager;
use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;
use ConditionalActions\Entities\State;
use Illuminate\Container\Container;

/**
 * @mixin TargetContract
 */
trait RunsConditionalActions
{
    private $useLogger = false;

    protected function newState(iterable $attributes): StateContract
    {
        return new State($attributes);
    }

    public function useLogger(bool $use = true): self
    {
        $this->useLogger = $use;

        return $this;
    }

    public function runConditionalActions()
    {
        /** @var ConditionActionManager $manager */
        $manager = Container::getInstance()->make(ConditionActionManager::class);
        $manager->useLogger = $this->useLogger;
        $manager->run($this, $this->getState());
    }
}
