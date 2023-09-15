<?php

namespace App\Events\Customer;

use App\Models\Customer\Customer;
use Illuminate\Queue\SerializesModels;

/**
 * Class CustomerStatusChanged.
 */
class CustomerStatusChanged
{
    use SerializesModels;

    /**
     * @var
     */
    public $customer;

    /**
     * @var
     */
    public $status;

    /**
     * CustomerStatusChanged constructor.
     *
     * @param  Customer  $customer
     * @param $status
     */
    public function __construct(Customer $customer, $status)
    {
        $this->customer = $customer;
        $this->status = $status;
    }
}
