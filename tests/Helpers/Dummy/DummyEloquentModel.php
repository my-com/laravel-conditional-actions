<?php

namespace Tests\Helpers\Dummy;

use ConditionalActions\Traits\EloquentTarget;
use Illuminate\Database\Eloquent\Model;

class DummyEloquentModel extends Model
{
    use EloquentTarget;

    protected $fillable = [
        'name',
    ];
}
