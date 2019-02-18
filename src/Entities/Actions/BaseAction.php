<?php

namespace ConditionalActions\Entities\Actions;

use ConditionalActions\Contracts\ActionContract;

abstract class BaseAction implements ActionContract
{
    protected $parameters = [];

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
}
