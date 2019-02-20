<?php

namespace Tests;

class EloquentTestCase extends TestCase
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
