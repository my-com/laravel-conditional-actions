<?php

namespace ConditionalActions\Contracts\TargetProviders;

interface ProvidesValidationData
{
    public function getValidationData(): array;
}
