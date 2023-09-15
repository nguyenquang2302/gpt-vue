<?php

namespace App\Models\Bank;

use App\Models\Bank\BankAttribute;
use App\Models\Bank\BankMethod;
use App\Models\Bank\BankRelationship;
use App\Models\Bank\BankScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Bank extends Model
{
    protected $table = "banks";
    use 
        SoftDeletes,
        BankAttribute,
        BankMethod,

        BankRelationship,
        BankScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'accId',
        'slug',
        'code',
        'bin',
        'shortName',
        'logo',
        'transferSupported',
        'lookupSupported',
        'short_name',
        'support',
        'isTransfer',
        'swift_code',
        'active',
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
