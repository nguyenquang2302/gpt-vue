<?php

namespace App\Models\FundCategory;

use App\Models\FundCategory\FundCategoryAttribute;
use App\Models\FundCategory\FundCategoryMethod;
use App\Models\FundCategory\FundCategoryRelationship;
use App\Models\FundCategory\FundCategoryScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class FundCategory extends Model
{
    protected $table = "fund_categories";
    use 
        SoftDeletes,
        FundCategoryAttribute,
        FundCategoryMethod,

        FundCategoryRelationship,
        FundCategoryScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
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
