<?php

namespace App\Events\FundCategory;

use App\Models\FundCategory\FundCategory;
use Illuminate\Queue\SerializesModels;

/**
 * Class FundCategoryStatusChanged.
 */
class FundCategoryStatusChanged
{
    use SerializesModels;

    /**
     * @var
     */
    public $FundCategory;

    /**
     * @var
     */
    public $status;

    /**
     * FundCategoryStatusChanged constructor.
     *
     * @param  FundCategory  $FundCategory
     * @param $status
     */
    public function __construct(FundCategory $FundCategory, $status)
    {
        $this->FundCategory = $FundCategory;
        $this->status = $status;
    }
}
