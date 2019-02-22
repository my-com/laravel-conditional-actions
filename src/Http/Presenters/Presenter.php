<?php

namespace ConditionalActions\Http\Presenters;

abstract class Presenter
{
    /**
     * @return IterablePresenter|static
     */
    public function collection(): IterablePresenter
    {
        return new IterablePresenter($this);
    }
}
