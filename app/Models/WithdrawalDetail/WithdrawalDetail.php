<?php

namespace App\Models\WithdrawalDetail;

use App\Models\WithdrawalDetail\WithdrawalDetailAttribute;
use App\Models\WithdrawalDetail\WithdrawalDetailMethod;
use App\Models\WithdrawalDetail\WithdrawalDetailRelationship;
use App\Models\WithdrawalDetail\WithdrawalDetailScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class WithdrawalDetail extends Model
{
    protected $table = "withdrawal_transaction_detail";
    use 
        SoftDeletes,
        WithdrawalDetailAttribute,
        WithdrawalDetailMethod,

        WithdrawalDetailRelationship,
        WithdrawalDetailScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'money',
        'money_drawal',
        'fee_bank',
        'fee_money_bank',
        'money_back',
        'time',
        'withdrawal_id',
        'user_id',
        'pos_id',
        'note',
        'active',
        'lo',
        'bill',
        'postingDate',
        'transactionDate',
        'refNo',
        'isBankChecked',
        'profit',
        'isOwnerPos',
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
