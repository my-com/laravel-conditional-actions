<?php

namespace Tests;

use ConditionalActions\ConditionalActionsServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Tests\Helpers\Dummy\DummyTestHelper;

class TestCase extends OrchestraTestCase
{
    use DummyTestHelper;

    protected function getPackageProviders($app)
    {
        return [ConditionalActionsServiceProvider::class];
    }
}
