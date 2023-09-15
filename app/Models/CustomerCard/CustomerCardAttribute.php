<?php

namespace App\Models\CustomerCard;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Trait CustomerCardAttribute.
 */
trait CustomerCardAttribute
{

    public function getDueDate2Attribute($value)
    {
        if($value) {
            return Carbon::parse($value)->format('d/m/Y');
        }
        return null;
    }
    public function getDateComlateAttribute($value)
    {
        if($value) {
            return Carbon::parse($value)->format('d/m/Y');
        }
        return null;
    }
}
