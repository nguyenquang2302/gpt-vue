<?php

namespace App\Models\CustomerSchedule;

use App\Models\CustomerSchedule\CustomerScheduleAttribute;
use App\Models\CustomerSchedule\CustomerScheduleMethod;
use App\Models\CustomerSchedule\CustomerScheduleRelationship;
use App\Models\CustomerSchedule\CustomerScheduleScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class CustomerSchedule extends Model
{
    protected $table = "customer_schedules";
    use 
        SoftDeletes,
        CustomerScheduleAttribute,
        CustomerScheduleMethod,

        CustomerScheduleRelationship,
        CustomerScheduleScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'note',
        'schedule',
        'brank_id',
        'customer_id',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

    /**
     * @var array
     */
    protected $dates = [
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $appends = [
    ];

    /**
     * @var string[]
     */
    protected $with = [
    ];
}
