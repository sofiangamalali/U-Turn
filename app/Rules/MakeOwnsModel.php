<?php

namespace App\Rules;

use App\Models\CarModel;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MakeOwnsModel implements ValidationRule
{
    public function __construct(protected int $makeId)
    {
        $this->makeId = $makeId;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!CarModel::where('id', $value)->where('car_make_id', $this->makeId)->exists()) {
            $fail('Make does not own this model');
        }
    }
}
