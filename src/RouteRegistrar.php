<?php

namespace ConditionalActions;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var Router
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param  Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     */
    public function all()
    {
        $this->router->resource('conditions', 'ConditionsController')
            ->only('store', 'show', 'update', 'destroy')
            ->names('conditions');

        $this->router->resource('actions', 'ActionsController')
            ->only('store', 'show', 'update', 'destroy')
            ->names('actions');
    }
}
