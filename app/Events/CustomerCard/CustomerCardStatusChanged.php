<?php

namespace App\Events\CustomerCard;

use App\Models\CustomerCard\CustomerCard;
use Illuminate\Queue\SerializesModels;

/**
 * Class CustomerCardStatusChanged.
 */
class CustomerCardStatusChanged
{
    use SerializesModels;

    /**
     * @var
     */
    public $customerCard;

    /**
     * @var
     */
    public $status;

    /**
     * CustomerCardStatusChanged constructor.
     *
     * @param  CustomerCard  $customerCard
     * @param $status
     */
    public function __construct(CustomerCard $customerCard, $status)
    {
        $this->customerCard = $customerCard;
        $this->status = $status;
    }
}
