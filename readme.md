# Conditional Actions

![Build Status](https://travis-ci.org/my-com/laravel-conditional-actions.svg?branch=master)

This package allows configuring business logic by API without changing your code.
This is helpful when you don`t know specific conditions because they defined dynamically by your managers/users/etc.

## How it works

Codebase provides predefined conditions, actions, targets and API for mix them into business logic to end users.
Objects:
* `Target` - provides all necessary data for conditions and actions;
* `State` - key-value pairs. Actions should update state when applying;
* `Condition` - condition has method `check`, it returns succeed it or not (bool);
* `Action` - action has method `apply`, it change `State` or make any other actions and returns changed `State`;

Lifecycle:
* `Target` creates a `State` object;
* `Target` gets all related active `Condition` sorted by priority and run the check on each condition;
* For succeeded `Condition`, `Condition` gets all related actions and apply them to the `State`;
* `Action` returns changed `State` which used in next conditions or actions;
* After checking all `Condition`, `Target` gets new `State` to `applyState` method. You can use its state as you needed.

## Get started

For example, you have a shop that sells toys. Your marketing runs some promotions for specific toys.
If a user buys chests in the past or today is his birthday, "Barbie doll" should have a 10% discount.
Promotion starts at 2019/05/01 at 00:00 and finishes at 2019/05/01 at 23:59.

You should create:

Conditions:
* User bought toys in the past (`HasPaidToysCondition`)
* Today is his birthday (`TodayIsBirthdayCondition`)

Action:
* "Barbie doll" should have a 10% discount (`DiscountAction`)

For time restrictions (Promotion starts at 2019/05/01 00:00 and finishes at 2019/05/01 23:59) you can use fields `starts_at` and `ends_at`.

Both conditions should be succeeded. You can use `AllOfCondition` condition from the package.

Marketing can use it for promotions without changing your code.

The final scheme for promotion:

```
■ AllOfCondition (condition)
│ # fields: ['id' => 1, 'starts_at' => '2019-05-01 00:00:00', 'ends_at' => '2019-05-01 23:59:59']
│    ║
│    ╚═» ░ DiscountAction (action)
│          # fields: ['parameters' => ['discount' => 10]]
│
├─── ■ TodayIsBirthdayCondition (condition)
│      # fields: ['parent_id' => 1]
│
└─── ■ HasPaidToysCondition (condition)
       # fields: ['parent_id' => 1, 'parameters' => ['toy_id' => 5]]
```

Let`s go to implementation!

### Install package
```bash
composer require my-com/laravel-conditional-actions
```

### Laravel
#### For versions < 5.5:
Add package service provider to `config/app.php`:
```php
return [
    // ...
    'providers' => [
        // ...
        ConditionalActions\ConditionalActionsServiceProvider::class,
    ],
    // ...
```
#### For laravel >= 5.5
Laravel 5.5 uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider

### Lumen
Register service provider and config in `app.php`:
```php
$app->configure('conditional-actions');
$app->register(ConditionalActions\ConditionalActionsServiceProvider::class);
```

### Add migrations
```bash
php artisan ca:tables
php artisan migrate
php artisan vendor:publish --provider="ConditionalActions\ConditionalActionsServiceProvider"
```

> Command options:
> ```bash
> Description:
>   Create a migration for the conditional actions database tables
> 
> Usage:
>   ca:tables [options]
> 
> Options:
>       --migrations-path[=MIGRATIONS-PATH]  Path to migrations directory (relative to framework base path) [default: "database/migrations"]
> ```

### Implement Target

Target is an object that provides all necessary data for conditions and actions. It can be also an eloquent model.

Since `Toy` - object for conditional actions, it should use `EloquentTarget` trait (trait has relationships and some method to get conditions for model)

```php
class ToysPriceTarget implements TargetContract
{
    use RunsConditionalActions;

    /** @var Toy */
    public $toy;

    /** @var User */
    public $user;

    public $finalPrice;

    public function __construct(Toy $toy, User $user)
    {
        $this->toy = $toy;
        $this->user = $user;
    }

    /**
     * Gets state from target.
     *
     * @return StateContract
     */
    public function getInitialState(): StateContract
    {
        return $this->newState([
            'price' => $this->toy->price,
        ]);
    }

    /**
     * Sets the state to the target.
     *
     * @param StateContract $state
     */
    public function applyState(StateContract $state): void
    {
        $this->finalPrice = $state->getAttribute('price');
    }

    /**
     * Gets root target conditions.
     *
     * @return iterable|ConditionContract[]
     */
    public function getRootConditions(): iterable
    {
        return $this->toy->getRootConditions();
    }

    /**
     * Gets children target conditions.
     *
     * @param int $parentId
     *
     * @return iterable|ConditionContract[]
     */
    public function getChildrenConditions(int $parentId): iterable
    {
        return $this->toy->getChildrenConditions($parentId);
    }
}
```

### Implement conditions

Each condition should implement `ConditionalActions\Contracts\ConditionContract` contract.
The package has a base abstract class `ConditionalActions\Entities\Conditions\BaseCondition` with all contract methods except the `check` method.

```php
class HasPaidToysCondition extends BaseCondition
{
    /** @var ToysService */
    private $toysService;

    // You can use dependency injection in constructor
    public function __construct(ToysService $toysService)
    {
        $this->toysService = $toysService;
    }

    /**
     * Runs condition check.
     *
     * @param TargetContract $target
     * @param StateContract $state
     *
     * @return bool
     */
    public function check(TargetContract $target, StateContract $state): bool
    {
        $toyId = $this->parameters['toy_id'] ?? null;

        if (!($target instanceof ToysPriceTarget) || is_null($toyId)) {
            return false;
        }

        return $this->toysService->hasPaidToy($target->user, $toyId);
    }
}
```

```php
class TodayIsBirthdayCondition extends BaseCondition
{
    /** @var ToysService */
    private $toysService;

    // You can use dependency injection in constructor
    public function __construct(ToysService $toysService)
    {
        $this->toysService = $toysService;
    }

    /**
     * Runs condition check.
     *
     * @param TargetContract $target
     * @param StateContract $state
     *
     * @return bool
     */
    public function check(TargetContract $target, StateContract $state): bool
    {
        if (!($target instanceof ToysPriceTarget)) {
            return false;
        }

        return Carbon::now()->isSameDay($target->user->birthday);
    }
}
```

### Implement action

Each condition should implement `ConditionalActions\Contracts\ActionContract` contract.
The package has a base abstract class `ConditionalActions\Entities\Actions\BaseAction` with all contract methods except the `apply` method.

```php
class DiscountAction extends BaseAction
{
    /**
     * Applies action to the state and returns a new state.
     *
     * @param StateContract $state
     *
     * @return StateContract
     */
    public function apply(StateContract $state): StateContract
    {
        $discount = $this->parameters['discount'] ?? 0;
        $currentPrice = $state->getAttribute('price');
        $state->setAttribute('price', $currentPrice - $currentPrice / 100 * $discount);

        return $state;
    }
}
```

### Add conditions to config `config/conditional-actions.php`

```php

return [
    'conditions' => [
        'AllOfCondition' => ConditionalActions\Entities\Conditions\AllOfCondition::class,
        'OneOfCondition' => ConditionalActions\Entities\Conditions\OneOfCondition::class,
        'TrueCondition' => ConditionalActions\Entities\Conditions\TrueCondition::class,

        'CurrentTimeCondition' => App\ConditionalActions\Conditions\CurrentTimeCondition::class,
        'HasPaidToysCondition' => App\ConditionalActions\Conditions\HasPaidToysCondition::class,
        'TodayIsBirthdayCondition' => App\ConditionalActions\Conditions\TodayIsBirthdayCondition::class,
    ],
    'actions' => [
        'UpdateStateAttributeAction' => ConditionalActions\Entities\Actions\UpdateStateAttributeAction::class,

        'DiscountAction' => App\ConditionalActions\Actions\DiscountAction::class,
    ],
    'use_logger' => env('APP_DEBUG', false),
];
```

### Implement API for adds conditions and actions for `Toy` model

You can use eloquent models or any other objects to put business logic into external storage.

The package has basic CRUD for conditions and actions. You can enable it:
```php
use ConditionalActions\ConditionalActions;
use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    // ...
    public function register()
    {
        ConditionalActions::routes();
    }
}
```

Or you can implement your own API. Sample example:
```php
# This example is not an API. You can create API as you needed.

/** @var Toy $toy */
$toy = Toy::find(10);

/** @var Condition $allOf */
$allOf = $toy->conditions()->create([
    'starts_at' => '2019-05-01 00:00:00',
    'ends_at' => '2019-05-01 23:59:59',
]);

$allOf->actions()->create([
    'name' => 'DiscountAction',
    'parameters' => ['discount' => 10],
]);

$todayIsBirthday = $allOf->childrenConditions()->make([
    'name' => 'TodayIsBirthdayCondition',
]);

$hasPaidToy = $allOf->childrenConditions()->make([
    'name' => 'HasPaidToysCondition',
    'parameters' => ['toy_id' => 5],
]);

$toy->conditions()->saveMany([$allOf, $hasPaidToy, $todayIsBirthday]);
```

### Run conditional actions

```php
$toy = Toy::find(10);

// Create a target instance
$target = new ToysPriceTarget(Auth::user(), $toy);
/*
 * Run conditional actions.
 * This method will iterate over all its conditions stored in database and apply actions related to succeed conditions
 */
$newState = $target->runConditionalActions();
dump($newState->getAttribute('price'));
```

## P.S.

The package includes conditions and actions:

* Condition `AllOfCondition` - succeeded when **all** children conditions are succeeded. All children actions will be included to parent `AllOfCondition` condition;
* Condition `OneOfCondition` - succeeded when **any of** children conditions are succeeded. All children actions for **first** succeeded condition will be included to parent `OneOfCondition` condition; 
* Condition `TrueCondition` - always succeeded;
* Action `UpdateStateAttributeAction` - Updates an attribute value in the state.

Both conditions and actions have fields:
* `priority` - execution priority;
* nullable `starts_at` and `ends_at` - enables condition or action at specific time period;
* `parameters` - parameters of conditions or actions;
* `is_inverted` - determines whether the condition result should be inverted.
