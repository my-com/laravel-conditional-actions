<?php

namespace ConditionalActions\Entities\Actions;

use ConditionalActions\Contracts\ActionContract;
use Illuminate\Support\Carbon;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
abstract class BaseAction implements ActionContract
{
    /** @var int|null */
    protected $id;

    /** @var int */
    protected $priority = 0;

    /** @var iterable */
    protected $parameters = [];

    /** @var Carbon|null */
    protected $startsAt;

    /** @var Carbon|null */
    protected $endsAt;

    /**
     * Sets the action parameters.
     *
     * @param array $parameters
     *
     * @return ActionContract
     */
    public function setParameters(?array $parameters): ActionContract
    {
        $this->parameters = $parameters ?? [];

        return $this;
    }

    /**
     * Gets action parameters.
     *
     * @return iterable
     */
    public function getParameters(): iterable
    {
        return $this->parameters;
    }

    /**
     * Sets the action identifier.
     *
     * @param int|null $id
     *
     * @return BaseAction
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets action identifier.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Sets the action priority.
     *
     * @param int $priority
     *
     * @return BaseAction
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Gets action priority.
     *
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * Sets the start time.
     *
     * @param Carbon|null $startsAt
     *
     * @return BaseAction
     */
    public function setStartsAt(?Carbon $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    /**
     * Gets start time.
     *
     * @return Carbon|null
     */
    public function getStartsAt(): ?Carbon
    {
        return $this->startsAt;
    }

    /**
     * Sets the finish time.
     *
     * @param Carbon|null $endsAt
     *
     * @return BaseAction
     */
    public function setEndsAt(?Carbon $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    /**
     * Gets finish time.
     *
     * @return Carbon|null
     */
    public function getEndsAt(): ?Carbon
    {
        return $this->endsAt;
    }
}
