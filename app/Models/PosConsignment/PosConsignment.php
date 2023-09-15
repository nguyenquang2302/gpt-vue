<?php

namespace App\Models\PosConsignment;

use App\Models\PosConsignment\PosConsignmentAttribute;
use App\Models\PosConsignment\PosConsignmentMethod;
use App\Models\PosConsignment\PosConsignmentRelationship;
use App\Models\PosConsignment\PosConsignmentScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class PosConsignment extends Model
{
    protected $table = "pos_consignments";
    use 
        SoftDeletes,
        PosConsignmentAttribute,
        PosConsignmentMethod,

        PosConsignmentRelationship,
        PosConsignmentScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pos_id',
        'lo',
        'isDone',
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
