<?php

namespace ConditionalActions\Traits;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Entities\Eloquent\Condition;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @mixin Model
 * @property Collection|ConditionContract[] conditions
 */
trait EloquentTarget
{
    public function conditions(): MorphMany
    {
        return $this->morphMany(Condition::class, 'target');
    }

    /**
     * Gets target conditions.
     *
     * @return iterable|ConditionContract[]
     */
    public function getRootConditions(): iterable
    {
        return $this->getChildrenConditions(null);
    }

    /**
     * Gets children target conditions.
     *
     * @param int $parentId
     *
     * @return iterable|ConditionContract[]
     */
    public function getChildrenConditions(?int $parentId): iterable
    {
        return $this->conditions
            ->filter(function (Condition $condition) use ($parentId) {
                return $condition->parent_id === $parentId && $condition->isActive();
            })
            ->sortBy('priority')
            ->map(function (Condition $condition) {
                return $condition->toCondition();
            })
            ->values();
    }
}
