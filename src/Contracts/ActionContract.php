<?php

namespace ConditionalActions\Contracts;

use ConditionalActions\Entities\Actions\BaseAction;
use Illuminate\Support\Carbon;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface ActionContract
{
    /**
     * Applies action to the state and returns a new state.
     *
     * @param StateContract $state
     *
     * @return StateContract
     */
    public function apply(StateContract $state): StateContract;

    /**
     * Sets the action parameters.
     *
     * @param array $parameters
     *
     * @return ActionContract
     */
    public function setParameters(?array $parameters): self;

    /**
     * Gets action parameters.
     *
     * @return iterable
     */
    public function getParameters(): iterable;

    /**
     * Gets action priority.
     *
     * @return int
     */
    public function getPriority(): int;

    /**
     * Gets finish time.
     *
     * @return Carbon|null
     */
    public function getEndsAt(): ?Carbon;

    /**
     * Sets the action priority.
     *
     * @param int $priority
     *
     * @return BaseAction
     */
    public function setPriority(int $priority): BaseAction;

    /**
     * Gets start time.
     *
     * @return Carbon|null
     */
    public function getStartsAt(): ?Carbon;

    /**
     * Sets the action identifier.
     *
     * @param int|null $id
     *
     * @return BaseAction
     */
    public function setId(?int $id): BaseAction;

    /**
     * Gets action identifier.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Sets the finish time.
     *
     * @param Carbon|null $endsAt
     *
     * @return BaseAction
     */
    public function setEndsAt(?Carbon $endsAt): BaseAction;

    /**
     * Sets the start time.
     *
     * @param Carbon|null $startsAt
     *
     * @return BaseAction
     */
    public function setStartsAt(?Carbon $startsAt): BaseAction;
}
