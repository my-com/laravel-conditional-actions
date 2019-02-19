<?php

namespace Tests\Dummy;

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
