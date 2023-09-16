<?php

namespace App\Events\FundCategory;

use App\Models\FundCategory\FundCategory;
use Illuminate\Queue\SerializesModels;

/**
 * Class FundCategoryDestroyed.
 */
class FundCategoryDestroyed
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
