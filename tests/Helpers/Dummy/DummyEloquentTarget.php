<?php

namespace Tests\Helpers\Dummy;

use ConditionalActions\Traits\EloquentTarget;
use Illuminate\Database\Eloquent\Model;

class DummyEloquentTarget extends Model
{
    use EloquentTarget;

    protected $fillable = [
        'name',
    ];
}
