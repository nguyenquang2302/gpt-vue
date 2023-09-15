<?php

namespace App\Models\Customer;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

/**
 * Trait CustomerAttribute.
 */
trait CustomerAttribute
{
   
public function getBirthDayAttribute($value)
{
    return Carbon::parse($value)->format('m/d/Y');
}

public function getNamePhoneattribute($value)
{
    return $this->name.' - '.$this->phone;
}

}
