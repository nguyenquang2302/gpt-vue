<?php

namespace App\Events\FundCategory;

use App\Models\FundCategory\FundCategory;
use Illuminate\Queue\SerializesModels;

/**
 * Class FundCategoryUpdated.
 */
class FundCategoryUpdated
{
    use SerializesModels;

    /**
     * @var
     */
    public $FundCategory;

    /**
     * @param $FundCategory
     */
    public function __construct(FundCategory $FundCategory)
    {
        $this->FundCategory = $FundCategory;
    }
}