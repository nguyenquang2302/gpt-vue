<?php

namespace App\Models\Expense;

use App\Models\Expense\ExpenseAttribute;
use App\Models\Expense\ExpenseMethod;
use App\Models\Expense\ExpenseRelationship;
use App\Models\Expense\ExpenseScope;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User.
 */
class Expense extends Model
{
    protected $table = "expense";
    use 
        SoftDeletes,
        ExpenseAttribute,
        ExpenseMethod,

        ExpenseRelationship,
        ExpenseScope;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'debitAmount',
        'creditAmount',
        'type',
        'fund_category_id',
        'note',
        'user_id',
        'active',
        'bank_log_id',
        'refNo',
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
