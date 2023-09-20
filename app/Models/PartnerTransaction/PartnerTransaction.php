<?php

namespace App\Models\PartnerTransaction;

use App\Models\PartnerTransaction\PartnerTransactionAttribute;
use App\Models\PartnerTransaction\PartnerTransactionMethod;
use App\Models\PartnerTransaction\PartnerTransactionRelationship;
use App\Models\PartnerTransaction\PartnerTransactionScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class PartnerTransaction extends Model
{
    protected $table = "partner_transactions";
    use
        SoftDeletes,
        PartnerTransactionAttribute,
        PartnerTransactionMethod,

        PartnerTransactionRelationship,
        PartnerTransactionScope;


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
        'note',
        'user_id',
        'active',
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
