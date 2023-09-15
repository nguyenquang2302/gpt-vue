<?php

namespace App\Models\Branch;

use App\Models\Branch\BranchAttribute;
use App\Models\Branch\BranchMethod;
use App\Models\Branch\BranchRelationship;
use App\Models\Branch\BranchScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Branch extends Model
{
    protected $table = "branchs";
    use 
        SoftDeletes,
        BranchAttribute,
        BranchMethod,

        BranchRelationship,
        BranchScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'branch_id',
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
