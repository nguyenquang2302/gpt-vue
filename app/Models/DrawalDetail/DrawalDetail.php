<?php

namespace App\Models\DrawalDetail;

use App\Models\DrawalDetail\DrawalDetailAttribute;
use App\Models\DrawalDetail\DrawalDetailMethod;
use App\Models\DrawalDetail\DrawalDetailRelationship;
use App\Models\DrawalDetail\DrawalDetailScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class DrawalDetail extends Model
{
    protected $table = "drawal_details";
    use 
        SoftDeletes,
        DrawalDetailAttribute,
        DrawalDetailMethod,

        DrawalDetailRelationship,
        DrawalDetailScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'money',
        'fee_bank',
        'fee_bank_money',
        'money_back',
        'time',
        'sort',
        'drawal_id',
        'user_id',
        'pos_id',
        'customer_card_id',
        'active',
        'lo',
        'bill',
        'profit',
        'branch_id'
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
        'group_bill'
    ];

    /**
     * @var string[]
     */
    protected $with = [
    ];
}
