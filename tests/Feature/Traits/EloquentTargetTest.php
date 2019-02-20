<?php

namespace Tests\Feature\Traits;

use ConditionalActions\Contracts\ConditionContract;
use ConditionalActions\Entities\Eloquent\Condition;
use Illuminate\Support\Collection;
use Tests\EloquentTestCase;
use Tests\Helpers\Dummy\DummyEloquentTarget;

class EloquentTargetTest extends EloquentTestCase
{
    public function test_get_root_and_children_conditions()
    {
        /** @var DummyEloquentTarget $target */
        $target = \create(DummyEloquentTarget::class);
        /** @var Condition[]|Collection $roots */
        $roots = \factory(Condition::class, 2)->create();
        $children = \create(Condition::class, ['parent_id' => $roots[1]->id]);
        \create(Condition::class, ['parent_id' => $roots[1]->id]);
        $target->conditions()->saveMany([$roots[1], $children]);

        $actualRoots = $target->getRootConditions();
        $actualChildren = $target->getChildrenConditions($roots[1]->id);

        $this->assertEquals(
            [$roots[1]->id],
            \iterator_to_array(\collect($actualRoots)->map->getId())
        );
        $this->assertEquals(
            [$children->id],
            \iterator_to_array(\collect($actualChildren)->map->getId())
        );
    }

    public function test_get_root_and_children_conditions_filtered_by_active()
    {
        /** @var DummyEloquentTarget $target */
        $target = \create(DummyEloquentTarget::class);
        /** @var Condition $activeRoot */
        $activeRoot = \create(Condition::class);
        $inactiveRoot = \create(Condition::class, [], 'inactive');
        $inactiveChildren = $activeRoot->childrenConditions()->save(\create(Condition::class, [], 'inactive'));
        /** @var Condition $activeChildren */
        $activeChildren = $activeRoot->childrenConditions()->save(\create(Condition::class));
        $target->conditions()->saveMany([$activeRoot, $inactiveRoot, $activeChildren, $inactiveChildren]);

        $actualRoots = $target->getRootConditions();
        $actualChildren = $target->getChildrenConditions($activeRoot->id);

        $this->assertEquals(
            [$activeRoot->id],
            \iterator_to_array(\collect($actualRoots)->map->getId())
        );
        $this->assertEquals(
            [$activeChildren->id],
            \iterator_to_array(\collect($actualChildren)->map->getId())
        );
    }

    public function test_get_root_and_children_conditions_sorted_by_priority()
    {
        /** @var DummyEloquentTarget $target */
        $target = \create(DummyEloquentTarget::class);
        /** @var Condition $root10 */
        [$root10, $root20, $root15] = \createMany(Condition::class, ['priority'], [[10], [20], [15]]);
        [$children10, $children20, $children15] = \createMany(Condition::class, ['priority'], [[10], [20], [15]]);
        $root10->childrenConditions()->saveMany([$children10, $children20, $children15]);
        $target->conditions()->saveMany([$root10, $root20, $root15, $children10, $children20, $children15]);

        $actualRoots = $target->getRootConditions();
        $actualChildren = $target->getChildrenConditions($root10->id);

        $this->assertEquals(
            [$root10->id, $root15->id, $root20->id],
            \iterator_to_array(\collect($actualRoots)->map->getId())
        );
        $this->assertEquals(
            [$children10->id, $children15->id, $children20->id],
            \iterator_to_array(\collect($actualChildren)->map->getId())
        );
    }

    public function test_get_root_and_children_conditions_returns_condition_contracts()
    {
        /** @var DummyEloquentTarget $target */
        $target = \create(DummyEloquentTarget::class);
        /** @var Condition $root */
        $root = \create(Condition::class);
        $children = $root->childrenConditions()->save(\create(Condition::class, ['priority' => 10]));
        $target->conditions()->saveMany([$root, $children]);

        $actualRoots = $target->getRootConditions();
        $actualChildren = $target->getChildrenConditions($root->id);

        $this->assertInstanceOf(ConditionContract::class, $actualRoots[0]);
        $this->assertInstanceOf(ConditionContract::class, $actualChildren[0]);
    }
}
