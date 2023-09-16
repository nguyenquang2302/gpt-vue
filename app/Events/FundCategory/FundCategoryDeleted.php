<?php

namespace App\Events\FundCategory;

use App\Models\FundCategory\FundCategory;
use Illuminate\Queue\SerializesModels;

/**
 * Class FundCategoryDeleted.
 */
class FundCategoryDeleted
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
