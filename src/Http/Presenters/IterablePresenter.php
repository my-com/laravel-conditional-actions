<?php

namespace ConditionalActions\Http\Presenters;

use RuntimeException;

class IterablePresenter
{
    /** @var Presenter */
    private $presenter;

    public function __construct(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    public function __call($name, $arguments)
    {
        if (!\method_exists($this->presenter, $name)) {
            throw new RuntimeException(
                \sprintf('Method %s not found in presenter %s', $name, \get_class($this->presenter))
            );
        }

        if (!\count($arguments)) {
            throw new RuntimeException('No arguments');
        }

        $items = \array_shift($arguments);

        if (!\is_iterable($items)) {
            throw new RuntimeException('Received items is not an iterable');
        }

        $response = [];

        foreach ($items as $i => $item) {
            $response[$i] = $this->presenter->{$name}($item);
        }

        return $response;
    }
}
