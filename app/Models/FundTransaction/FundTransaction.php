<?php

namespace App\Models\FundTransaction;

use App\Models\FundTransaction\FundTransactionAttribute;
use App\Models\FundTransaction\FundTransactionMethod;
use App\Models\FundTransaction\FundTransactionRelationship;
use App\Models\FundTransaction\FundTransactionScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class FundTransaction extends Model
{
    protected $table = "fund_transactions";
    use
        SoftDeletes,
        FundTransactionAttribute,
        FundTransactionMethod,

        FundTransactionRelationship,
        FundTransactionScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'debitAmount',
        'creditAmount',
        'money_after',
        'money_before',
        'type',
        'fund_category_id',
        'note',
        'user_id',
        'active',
        'bank_log_id',
        'refNo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * @var array
     */
    protected $dates = [];

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
    protected $appends = [];

    /**
     * @var string[]
     */
    protected $with = [];
}
