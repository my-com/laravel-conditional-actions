<?php

namespace Tests\Helpers\Dummy;

use ConditionalActions\Contracts\ActionContract;
use PHPUnit\Framework\Assert;

trait DummyTestHelper
{
    protected $id = 5;

    /**
     * @param int $count
     *
     * @return Action[]
     */
    protected function makeActions(int $count = 1): array
    {
        $actions = [];

        for ($i = 0; $i < $count; $i++) {
            $actions[] = new Action();
        }

        return $actions;
    }

    protected function succeedCondition(?int $parentId = null, ActionContract ...$actions): DummyCondition
    {
        return DummySucceedCondition::withActions(++$this->id, $parentId, ...$actions);
    }

    protected function failedCondition(?int $parentId = null, ActionContract ...$actions): DummyCondition
    {
        return DummyFailedCondition::withActions(++$this->id, $parentId, ...$actions);
    }

    protected function assertFired(CanBeFired ...$items): void
    {
        foreach ($items as $item) {
            Assert::assertTrue($item->isFired());
        }
    }

    protected function assertNotFired(CanBeFired ...$items): void
    {
        foreach ($items as $item) {
            Assert::assertFalse($item->isFired());
        }
    }
}
