<?php

namespace App\Models\CustomerCard;

use App\Models\CustomerCard\CustomerCardAttribute;
use App\Models\CustomerCard\CustomerCardMethod;
use App\Models\CustomerCard\CustomerCardRelationship;
use App\Models\CustomerCard\CustomerCardScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class CustomerCard extends Model
{
    protected $table = "customer_cards";
    use 
        SoftDeletes,
        CustomerCardAttribute,
        CustomerCardMethod,

        CustomerCardRelationship,
        CustomerCardScope;


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
        'start_number',
        'end_number',
        'day_statement',
        'due_date',
        'card_number',
        'currency_limit',
        'type',
        'active',
        'customer_id',
        'bank_id',
        'note',
        'combo_time',
        'save',
        'due_date2',
        'currency_payment',
        'date_comlate',
        'branch_id',
        'account_number',
        'is_account'
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
        'combo_time',
        'due_date2',
        'date_comlate',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'is_account' => 'boolean',
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
