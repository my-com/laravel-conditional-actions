<?php

namespace ConditionalActions\Entities\Eloquent;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Exceptions\ConditionNotFoundException;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * @property int id
 * @property string target_type
 * @property int target_id
 * @property string name
 * @property array|null parameters
 * @property bool is_inverted
 * @property int priority
 * @property int parent_id
 * @property Carbon|null starts_at
 * @property Carbon|null ends_at
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 * @property Carbon|null deleted_at
 * @property Collection|ConditionAction[] conditionActions
 */
class Condition extends Model
{
    protected $fillable = [
        'target_type',
        'target_id',
        'name',
        'parameters',
        'is_inverted',
        'priority',
        'parent_id',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'parameters' => 'array',
        'is_inverted' => 'boolean',
    ];

    public function conditionActions(): HasMany
    {
        return $this->hasMany(ConditionAction::class);
    }

    public function childrenConditions(): HasMany
    {
        return $this->hasMany(Condition::class, 'parent_id', 'id');
    }

    public function isActive(): bool
    {
        return Carbon::now()->between(
            $this->starts_at ?? Carbon::minValue(),
            $this->ends_at ?? Carbon::maxValue()
        );
    }

    /**
     * @throws \Throwable
     *
     * @return ConditionContract
     */
    public function toCondition(): ConditionContract
    {
        $className = \config("conditional-actions.conditions.{$this->name}");

        \throw_unless(
            $className,
            ConditionNotFoundException::class,
            \sprintf('Condition %s not found', $this->name),
            Response::HTTP_NOT_FOUND
        );

        /** @var ConditionContract $condition */
        $condition = \app($className);

        return $condition->setId($this->id)
            ->setActions($this->getActiveActions()->map(function (ConditionAction $action) {
                return $action->toAction();
            }))
            ->setIsInverted($this->is_inverted)
            ->setParameters($this->parameters);
    }

    /**
     * @return ConditionAction[]|Collection
     */
    public function getActiveActions()
    {
        return $this->conditionActions
            ->filter(function (ConditionAction $action) {
                return $action->isActive();
            })
            ->sortBy('priority')
            ->values();
    }
}
