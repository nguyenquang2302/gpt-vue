<?php

namespace App\Models\Drawal;

use App\Models\Drawal\DrawalAttribute;
use App\Models\Drawal\DrawalMethod;
use App\Models\Drawal\DrawalRelationship;
use App\Models\Drawal\DrawalScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Drawal extends Model
{
    protected $table = "drawals";
    use 
        SoftDeletes,
        DrawalAttribute,
        DrawalMethod,

        DrawalRelationship,
        DrawalScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name",
        "transfer",
        "money",
        "user_fee_id",
        "fee_bank",
        "fee_bank_money",
        "money_drawal",
        "fee_customer",
        "fee_ship",
        "fee_user",
        "fee_money_customer",
        "note",
        "active",
        "user_id",
        "customer_id",
        "profit_money",
        'bank_id',
        'bank_customer_name',
        'bank_code',
        'money',
        'status',
        'profit',
        'datetime',
        'branch_id',
        'stt',
        'isDone'
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
        'datetime'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'isDone' => 'boolean',
        'datetime'=> 'datetime'
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
    
    public static function boot()
    {
        parent::boot();

        
    }
}
