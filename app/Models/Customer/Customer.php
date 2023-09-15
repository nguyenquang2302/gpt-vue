<?php

namespace App\Models\Customer;

use App\Models\Customer\CustomerAttribute;
use App\Models\Customer\CustomerMethod;
use App\Models\Customer\CustomerRelationship;
use App\Models\Customer\CustomerScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Customer extends Model
{
    protected $table = "customers";
    use 
        SoftDeletes,
        CustomerAttribute,
        CustomerMethod,

        CustomerRelationship,
        CustomerScope;

    public const TYPE_CUSTOMER = 1;
    public const TYPE_INVESTORS = 2;
    protected $dateFormat = 'Y-m-d';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'cmnd',
        'phone',
        'address',
        'note',
        'image',
        'active',
        'province_id',
        'district_id',
        'ward_id',
        'birth_day',
        'user_id',
        'money',
        'slug',
        'request_vip',
        'vip',
        'vip_time',
        'vip_time_expires',
        'introduced_by_user_id',
        'last_transaction_time',
        'branch_id',
        'type'
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
        'birth_day',
        'vip_time',
        'last_transaction_time'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'birth_day' => 'date:d/m/Y',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'name_phone'
    ];

    /**
     * @var string[]
     */
    protected $with = [
        'ward',
        'district',
        'province',
        'branch'
    ];
}
