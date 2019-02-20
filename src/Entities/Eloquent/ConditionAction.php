<?php

namespace ConditionalActions\Entities\Eloquent;

use ConditionalActions\Contracts\ActionContract;
use ConditionalActions\Exceptions\ConditionActionNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;

/**
 * @property int id
 * @property int condition_id
 * @property string name
 * @property array|null parameters
 * @property int priority
 * @property Carbon|null starts_at
 * @property Carbon|null ends_at
 * @property Carbon|null created_at
 * @property Carbon|null updated_at
 */
class ConditionAction extends Model
{
    protected $fillable = [
        'condition_id',
        'name',
        'parameters',
        'priority',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'parameters' => 'array',
        'priority' => 'int',
        'condition_id' => 'int',
    ];

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
     * @return ActionContract
     */
    public function toAction(): ActionContract
    {
        $className = \config("conditional-actions.actions.{$this->name}");

        \throw_unless(
            $className,
            ConditionActionNotFoundException::class,
            \sprintf('Action %s not found', $this->name),
            Response::HTTP_NOT_FOUND
        );

        /** @var ActionContract $action */
        $action = \app($className);

        return $action->setParameters($this->parameters);
    }
}
