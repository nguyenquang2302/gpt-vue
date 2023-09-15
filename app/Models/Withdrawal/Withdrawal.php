<?php

namespace App\Models\Withdrawal;

use App\Models\Withdrawal\WithdrawalAttribute;
use App\Models\Withdrawal\WithdrawalMethod;
use App\Models\Withdrawal\WithdrawalRelationship;
use App\Models\Withdrawal\WithdrawalScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Withdrawal extends Model
{
    protected $table = "withdrawals";
    use 
        SoftDeletes,
        WithdrawalAttribute,
        WithdrawalMethod,

        WithdrawalRelationship,
        WithdrawalScope;


    public const TYPE_VISA = 'visa';
    public const TYPE_JCB = 'jcb';
    public const TYPE_MASTER = 'master';
    public const TYPE_NAPAS = 'napas';
    public const TYPE_AMERICA_EXPREES = 'america_exprees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'money_withdrawal',
        'fee_customer',
        'fee_ship',
        'fee_user',
        'fee_money_customer',
        'profit_money',
        'isDone',
        'customer_id',
        'user_id',
        'user_fee_id',
        'customer_card_id',
        'note',
        'active',
        'status',
        'profit',
        'datetime',
        'isOwnerPos',
        'branch_id',
        'stt'
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
}
