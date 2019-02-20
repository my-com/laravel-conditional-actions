# Conditional actions

This package allows to run your predefined business logic in some predefined conditions defined by user.
This is helpful when you don`t know specific conditions because it defined dynamically by your managers/users/etc.

## How it works

Codebase provide predefined conditions, actions, targets and API for mix it into business logic to end users.
Objects:
* `Target` - provides all necessary data for conditions and actions;
* `State` - key-value pairs. Actions should update state when applying;
* `Condition` - condition has method `check`, it returns succeed it or not (bool);
* `Action` - action has method `apply`, it change `State` or make any other actions and returns changed `State`;

Lifecycle:
* `Target` creates a `State` object;
* `Target` gets all related active `Condition` sorted by priority and run check on each condition;
* For succeeded `Condition`, `Condition` gets all related actions and apply it to `State`;
* `Action` return changed `State` which uses in next conditions or actions;
* After checking all `Condition`, `Target` gets new `State` to `setState` method. You can use it state as you needed.

## Get started

For example, you have a shop that sells toys. You marketing runs some promotions for specific toys.
For example, if user buys chests in the past or today is his birthday, "Barbie doll" must have an discount.
Promotion starts at 2019/05/01 00:00 and finishes at 2019/05/01 23:59.

You should create:

Conditions:
* Promotion starts at 2019/05/01 00:00 and finishes at 2019/05/01 23:59 (`CurrentTimeCondition`)
* User buys toys in the past (`HasPaidToysCondition`)
* Today is his birthday (`TodayIsBirthdayCondition`)

Action:
* "Barbie doll" should have an discount (`DiscountAction`)

Marketing can use it for promotions without change you code.

Let`s go to implement it!

### Install package
```bash
composer require xxxcoltxxx/conditional-actions
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

Target is object that provide all necessary data for conditions and actions. It can be also an eloquent model.

Since `Toy` - object for conditional actions, he should use `EloquentTarget` trait (trait has relationships and some method to get conditions for model)

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
    public function getState(): StateContract
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
    public function setState(StateContract $state): void
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

Each conditions must implement `ConditionalActions\Contracts\ConditionContract` contract.
The package have base abstract class `ConditionalActions\Entities\Conditions\BaseCondition` with all contract methods except the `check` method.

```php
class CurrentTimeCondition extends BaseCondition
{
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
        $startsAt = $this->parameters['starts_at'] ?? null;
        $finishesAt = $this->parameters['finishes_at'] ?? null;

        return Carbon::now()->between(
            $startsAt ?? Carbon::minValue(),
            $finishesAt ?? Carbon::maxValue()
        );
    }
}
```

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

Each conditions must implement `ConditionalActions\Contracts\ConditionActionContract` contract.
The package have base abstract class `ConditionalActions\Entities\Actions\BaseConditionAction` with all contract methods except the `apply` method.

```php
class DiscountAction extends BaseConditionAction
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
        'AllOf' => ConditionalActions\Entities\Conditions\AllOfCondition::class,
        'OneOf' => ConditionalActions\Entities\Conditions\OneOfCondition::class,
        'True' => ConditionalActions\Entities\Conditions\TrueCondition::class,

        'CurrentTimeCondition' => App\ConditionalActions\Conditions\CurrentTimeCondition::class,
        'HasPaidToysCondition' => App\ConditionalActions\Conditions\HasPaidToysCondition::class,
        'TodayIsBirthdayCondition' => App\ConditionalActions\Conditions\TodayIsBirthdayCondition::class,
    ],
    'actions' => [
        'UpdateStateAttribute' => ConditionalActions\Entities\Actions\UpdateStateAttributeAction::class,

        'DiscountAction' => App\ConditionalActions\Actions\DiscountAction::class,
    ],
    'use_logger' => env('APP_DEBUG', false),
];
```

### Implement API for adds conditions and actions for `Toy` model

TODO

### Run conditional actions

```php
$toy = Toy::find(10);

// Create a target instance
$target = new ToysPriceTarget(Auth::user(), $toy);
/*
 * Run conditional actions.
 * This method will iterate over all its conditions stored in database and apply actions related to succeed conditions
 */
$target->runConditionalActions();
dump($target->finalPrice);
```

## P.S.

The package includes conditions and actions:

* Condition `AllOf` - run condition actions (and actions for children conditions) when **all** children conditions is succeeded;
* Condition `OneOf` - run condition actions (and actions for first succeeded children condition) when **any of** children conditions is succeeded; 
* Condition `True` - always runs actions;
* Action `UpdateStateAttribute` - Updates an attribute value to the state.

Both conditions and actions has fields:
* `priority` - run order;
* nullable `starts_at` and `ends_at` - enable or disable its based on current time;
* `parameters` - parameters of conditions or actions;
* `is_inverted` - Determines whether the condition result should be inverted.
