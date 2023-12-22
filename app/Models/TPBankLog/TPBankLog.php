<?php

namespace App\Models\TPBankLog;

use App\Models\TPBankLog\TPBankLogAttribute;
use App\Models\TPBankLog\TPBankLogMethod;
use App\Models\TPBankLog\TPBankLogRelationship;
use App\Models\TPBankLog\TPBankLogScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class TPBankLog extends Model
{
    protected $table = "tp_bank_logs";
    use 
        SoftDeletes,
        TPBankLogAttribute,
        TPBankLogMethod,

        TPBankLogRelationship,
        TPBankLogScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "refNo",
        "arrangementId",
        "reference",
        "description",
        "category",
        "bookingDate",
        "valueDate",
        "amount",
        "currency",
        "creditDebitIndicator",
        "runningBalance",
        "ofsAcctNo",
        "ofsAcctName",
        "creditorBankNameVn",
        "creditorBankNameEn",
        'creditAmount',
        'debitAmount',
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
