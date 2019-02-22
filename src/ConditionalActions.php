<?php

namespace ConditionalActions;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

class ConditionalActions
{
    /** @var string[] */
    private $conditionNames;

    /** @var string[] */
    private $actionNames;

    protected function loadConfig($force = false)
    {
        if (!$force && !\is_null($this->conditionNames)) {
            return;
        }

        $this->conditionNames = \array_flip(\config('conditional-actions.conditions', []));
        $this->actionNames = \array_flip(\config('conditional-actions.actions', []));
    }

    public function getActionNames(): array
    {
        $this->loadConfig();

        return $this->actionNames;
    }

    public function getActionName(string $className): ?string
    {
        $this->loadConfig();

        return Arr::get($this->actionNames, $className);
    }

    public function getConditionNames(): array
    {
        $this->loadConfig();

        return $this->conditionNames;
    }

    public function getConditionName(string $className): ?string
    {
        $this->loadConfig();

        return Arr::get($this->conditionNames, $className);
    }

    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };
        $defaultOptions = [
            'prefix' => 'api/v1/conditional-actions',
            'namespace' => 'ConditionalActions\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);
        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }
}
