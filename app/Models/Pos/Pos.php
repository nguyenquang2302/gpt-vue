<?php

namespace App\Models\Pos;

use App\Models\Pos\PosAttribute;
use App\Models\Pos\PosMethod;
use App\Models\Pos\PosRelationship;
use App\Models\Pos\PosScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Pos extends Model
{
    protected $table = "pos";
    use 
        SoftDeletes,
        PosAttribute,
        PosMethod,

        PosRelationship,
        PosScope;


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
        'currency_limit',
        'currency_limit_on_card',
        'bill_limit_on_card',
        'fee_bank',
        'user_id',
        'user_id_belongto',
        'currency_limit_on_bill',
        'bank_id',
        'note',
        'active',
        'telegramChanelId'

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
        'caculator_limit',
        'urlQR'
    ];
    /**
     * @var string[]
     */
    protected $with = [
    ];
}
