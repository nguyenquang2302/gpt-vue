<?php

namespace App\Events\Customer;

use App\Models\Customer\Customer;
use Illuminate\Queue\SerializesModels;

/**
 * Class CustomerRestored.
 */
class CustomerRestored
{
    use SerializesModels;

    /**
     * @var
     */
    public $customer;

    /**
     * @param $customer
     */
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
}
