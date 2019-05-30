<?php

namespace Tests;

class EloquentConditionalActionsTestCase extends ConditionalActionsTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->withFactories(realpath(\dirname(__DIR__) . '/tests/database/factories'));
        $this->loadMigrationsFrom([
            '--database' => 'testing',
            '--path' => realpath(\dirname(__DIR__) . '/tests/database/migrations'),
        ]);
    }
}
