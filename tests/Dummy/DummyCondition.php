<?php

namespace Tests\Dummy;

use ConditionalActions\Contracts\ActionContract;
use ConditionalActions\Entities\Conditions\BaseCondition;

abstract class DummyCondition extends BaseCondition
{
    /** @var int */
    protected $parentId;

    /** @var bool */
    public $isFired = false;

    public function __construct(int $id, int $parentId)
    {
        $this->id = $id;
        $this->parentId = $parentId;
    }

    public static function withActions(int $id, int $parentId, ActionContract ...$actions): self
    {
        return \tap(new static($id, $parentId), function (self $condition) use ($actions) {
            $condition->setActions($actions);
        });
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getParentId(): int
    {
        return $this->parentId;
    }
}
