<?php

namespace Tests\Helpers\Dummy;

use ConditionalActions\Contracts\TargetProviders\ProvidesValidationData;

class DummyValidationDataTarget extends DummyTarget implements ProvidesValidationData
{
    /** @var array */
    private $validationData;

    public function __construct(array $validationData)
    {
        parent::__construct();
        $this->validationData = $validationData;
    }

    public function getValidationData(): array
    {
        return $this->validationData;
    }
}
