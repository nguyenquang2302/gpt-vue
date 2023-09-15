<?php

namespace App\Events\CustomerCard;

use App\Models\CustomerCard\CustomerCard;
use Illuminate\Queue\SerializesModels;

/**
 * Class CustomerCardCreated.
 */
class CustomerCardCreated
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
