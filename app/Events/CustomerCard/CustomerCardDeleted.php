<?php

namespace App\Events\CustomerCard;

use App\Models\CustomerCard\CustomerCard;
use Illuminate\Queue\SerializesModels;

/**
 * Class CustomerCardDeleted.
 */
class CustomerCardDeleted
{
    use SerializesModels;

    /**
     * @var
     */
    public $customerCard;

    /**
     * @param $customerCard
     */
    public function __construct(CustomerCard $customerCard)
    {
        $this->customerCard = $customerCard;
    }
}
