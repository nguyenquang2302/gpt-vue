<?php

namespace App\Models\CustomerTransaction;

use App\Models\CustomerTransaction\CustomerTransactionAttribute;
use App\Models\CustomerTransaction\CustomerTransactionMethod;
use App\Models\CustomerTransaction\CustomerTransactionRelationship;
use App\Models\CustomerTransaction\CustomerTransactionScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class CustomerTransaction extends Model
{
    protected $table = "customer_transaction";
    use 
        SoftDeletes,
        CustomerTransactionAttribute,
        CustomerTransactionMethod,

        CustomerTransactionRelationship,
        CustomerTransactionScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank_id',
        'refNo',
        'money_before',
        'money_after',
        'money',
        'customer_id',
        'content',
        'source',
        'key',
        'bank_code',
        'bank_customer_name',
        'note',
        'postingDate',
        'transactionDate',
        'active',
        'bank_log_id'
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
