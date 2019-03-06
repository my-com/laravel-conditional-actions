<?php

namespace ConditionalActions\Contracts\Repositories;

use ConditionalActions\Contracts\ConditionContract;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface ConditionRepository
{
    public function find(int $id): ConditionContract;

    public function store(array $attributes): ConditionContract;

    public function update(int $id, array $attributes): ConditionContract;

    public function destroy(int $id): ConditionContract;
}
