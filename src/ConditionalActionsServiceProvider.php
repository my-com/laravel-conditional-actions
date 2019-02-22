<?php

namespace ConditionalActions;

use ConditionalActions\Console\ConditionalActionsTable;
use ConditionalActions\Contracts\Repositories\ActionRepository;
use ConditionalActions\Contracts\Repositories\ConditionRepository;
use ConditionalActions\Repositories\EloquentActionRepository;
use ConditionalActions\Repositories\EloquentConditionRepository;
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
        $this->app->singleton(ConditionalActions::class);
        $this->app->bind(ActionRepository::class, EloquentActionRepository::class);
        $this->app->bind(ConditionRepository::class, EloquentConditionRepository::class);
    }
}
