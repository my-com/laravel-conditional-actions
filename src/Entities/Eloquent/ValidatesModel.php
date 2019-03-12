<?php

namespace ConditionalActions\Entities\Eloquent;

use ConditionalActions\Traits\UsesValidation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

/**
 * @mixin Model
 */
trait ValidatesModel
{
    public static function bootValidatesModel()
    {
        static::saving(function (Model $model) {
            if ($model instanceof UsesValidation) {
                $validator = Validator::make(
                    $model->attributesToArray(),
                    static::validatingRules()
                );

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
            }
        });
    }
}
