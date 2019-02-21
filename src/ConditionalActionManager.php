<?php

namespace ConditionalActions;

use ConditionalActions\Contracts\StateContract;
use ConditionalActions\Contracts\TargetContract;
use Psr\Log\LoggerInterface;

class ConditionalActionManager
{
    /** @var LoggerInterface */
    private $logger;

    public $useLogger = false;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function run(TargetContract $target, StateContract $state): StateContract
    {
        $this->log(\sprintf('Initial state: %s', \json_encode($state->toArray())));

        foreach ($target->getRootConditions() as $condition) {
            $conditionName = \sprintf(
                '%s "%s"',
                $condition->isInverted() ? 'Inverted condition' : 'Condition',
                \class_basename($condition)
            );
            if ($condition->check($target, $state) !== $condition->isInverted()) {
                $this->log("[OK]\t" . $conditionName);

                foreach ($condition->getActions() as $action) {
                    $state = $action->apply($state);

                    $this->log(\sprintf('  Apply action "%s"', \class_basename($action)));
                    $this->log(\sprintf('    => New state: %s', \json_encode($state->toArray())));
                }
            } else {
                $this->log("[SKIP]\t" . $conditionName);
            }
        }

        $target->applyState($state);
        $this->log(\sprintf('Finish state: %s', \json_encode($state->toArray())));

        return $state;
    }

    protected function log(string $message)
    {
        if (!$this->useLogger) {
            return;
        }

        $this->logger->debug($message);
    }
}
