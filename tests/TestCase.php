<?php

namespace Tests;

use ConditionalActions\ConditionalActionsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [ConditionalActionsServiceProvider::class];
    }
}
