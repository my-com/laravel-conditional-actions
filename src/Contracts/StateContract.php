<?php

namespace ConditionalActions\Contracts;

interface StateContract
{
    public function setAttributes(iterable $attributes): void;

    public function setAttribute(string $path, $value): void;

    public function getAttribute(string $path, $default = null);

    public function toArray(): iterable;

    public function fromArray(array $array);
}
