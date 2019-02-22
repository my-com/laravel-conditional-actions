<?php

namespace Tests;

use ConditionalActions\ConditionalActionsServiceProvider;
use Illuminate\Support\Carbon;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Tests\Helpers\Dummy\DummyAction;
use Tests\Helpers\Dummy\DummyFailedCondition;
use Tests\Helpers\Dummy\DummySucceedCondition;
use Tests\Helpers\Dummy\DummyTestHelper;

class TestCase extends OrchestraTestCase
{
    use DummyTestHelper;

    protected function setUp()
    {
        parent::setUp();

        \config([
            'app.debug' => true,
            'conditional-actions.conditions.DummySucceedCondition' => DummySucceedCondition::class,
            'conditional-actions.conditions.DummyFailedCondition' => DummyFailedCondition::class,
            'conditional-actions.actions.DummyAction' => DummyAction::class,
        ]);

        Carbon::serializeUsing(function (Carbon $carbon) {
            return $carbon->toIso8601String();
        });
    }

    protected function getPackageProviders($app)
    {
        return [ConditionalActionsServiceProvider::class];
    }
}
