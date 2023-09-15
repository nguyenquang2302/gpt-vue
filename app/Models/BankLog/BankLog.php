<?php

namespace App\Models\BankLog;

use App\Models\BankLog\BankLogAttribute;
use App\Models\BankLog\BankLogMethod;
use App\Models\BankLog\BankLogRelationship;
use App\Models\BankLog\BankLogScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class BankLog extends Model
{
    protected $table = "bank_logs";
    use 
        SoftDeletes,
        BankLogAttribute,
        BankLogMethod,

        BankLogRelationship,
        BankLogScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "refNo",
        'transactionDate',
        'accountNo',
        'creditAmount',
        'debitAmount',
        'currency',
        'description',
        'availableBalance',
        'beneficiaryAccount',
        'refNo',
        'benAccountName',
        'bankName',
        'benAccountNo',
        'dueDate',
        'transactionType',
        'user_id',
        'user_id_belongto',
        'isChecked',
        'active',
        'content',
        'content_fix',
        'note',
        'name',
        'fund_category_id',
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
        'isChecked' => 'boolean',
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
