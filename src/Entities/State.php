<?php

namespace ConditionalActions\Entities;

use ConditionalActions\Contracts\StateContract;
use Illuminate\Support\Arr;

class State implements StateContract
{
    protected $attributes = [];

    public function __construct(iterable $attributes)
    {
        $this->setAttributes($attributes);
    }

    public function setAttributes(iterable $attributes): void
    {
        foreach ($attributes as $attribute => $value) {
            $this->attributes[$attribute] = $value;
        }
    }

    public function setAttribute(string $path, $value): void
    {
        Arr::set($this->attributes, $path, $value);
    }

    public function getAttribute(string $path, $default = null)
    {
        return Arr::get($this->attributes, $path, $default);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function fromArray(array $array)
    {
        $this->setAttributes($array);
    }
}
