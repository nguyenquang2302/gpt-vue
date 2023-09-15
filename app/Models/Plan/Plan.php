<?php

namespace App\Models\Plan;

use App\Models\Plan\PlanAttribute;
use App\Models\Plan\PlanMethod;
use App\Models\Plan\PlanRelationship;
use App\Models\Plan\PlanScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Plan extends Model
{
    protected $table = "plan";
    use 
        SoftDeletes,
        PlanAttribute,
        PlanMethod,

        PlanRelationship,
        PlanScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        't1',
        'plan1',
        'ex_plan1',
        't2',
        'plan2',
        'ex_plan2',
        't3',
        'plan3',
        'ex_plan3',
        't4',
        'plan4',
        'ex_plan4',
        't5',
        'plan5',
        'ex_plan5',
        't6',
        'plan6',
        'ex_plan6',
        't7',
        'plan7',
        'ex_plan7',
        't8',
        'plan8',
        'ex_plan8',
        't9',
        'plan9',
        'ex_plan9',
        't10',
        'plan10',
        'ex_plan10',
        't11',
        'plan11',
        'ex_plan11',
        't12',
        'plan12',
        'ex_plan12',
        't13',
        'plan13',
        'ex_plan13',
        't14',
        'plan14',
        'ex_plan14',
        't15',
        'plan15',
        'ex_plan15',
        't16',
        'plan16',
        'ex_plan16',
        'your_success',
        'grateful_for',
        'lesson',
        'distraction',
        'not_reached',
        'active',
        'user_id',
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
