<?php

namespace App\Models\GlobalDetail;

use App\Models\GlobalDetail\GlobalDetailAttribute;
use App\Models\GlobalDetail\GlobalDetailMethod;
use App\Models\GlobalDetail\GlobalDetailRelationship;
use App\Models\GlobalDetail\GlobalDetailScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class GlobalDetail extends Model
{
    protected $table = "global_details";
    use 
        SoftDeletes,
        GlobalDetailAttribute,
        GlobalDetailMethod,

        GlobalDetailRelationship,
        GlobalDetailScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'totalTransactions',
        'totalDrawals',
        'totalCustomerNew',
        'fee_ship',
        'expense',
        'totalProfit',
        'perDay',
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
        'perDay'
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
