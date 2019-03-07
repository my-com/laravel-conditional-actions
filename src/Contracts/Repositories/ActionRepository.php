<?php

namespace ConditionalActions\Contracts\Repositories;

use ConditionalActions\Contracts\ActionContract;

/**
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface ActionRepository
{
    public function find(int $id): ActionContract;

    public function store(array $attributes): ActionContract;

    public function update(int $id, array $attributes): ActionContract;

    public function destroy(int $id): ActionContract;
}
