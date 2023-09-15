<?php

namespace App\Models\PosBack;

use App\Models\PosBack\PosBackAttribute;
use App\Models\PosBack\PosBackMethod;
use App\Models\PosBack\PosBackRelationship;
use App\Models\PosBack\PosBackScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class PosBack extends Model
{
    protected $table = "pos_backs";
    use 
        SoftDeletes,
        PosBackAttribute,
        PosBackMethod,

        PosBackRelationship,
        PosBackScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'money',
        'pos_id',
        'lo',
        'note',
        'refNo',
        'postingDate',
        'transactionDate',
        'active',
        'bank_log_id',
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
