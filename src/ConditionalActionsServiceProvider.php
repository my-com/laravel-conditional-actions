<?php

namespace ConditionalActions;

use ConditionalActions\Console\ConditionalActionsTable;
use Illuminate\Support\ServiceProvider;

class ConditionalActionsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/conditional-actions.php',
            'conditional-actions'
        );

        $this->publishes([
            __DIR__ . '/../config/conditional-actions.php' => \config_path('conditional-actions.php'),
        ]);

        $this->commands([
            ConditionalActionsTable::class,
        ]);
    }

    public function register()
    {
    }
}
