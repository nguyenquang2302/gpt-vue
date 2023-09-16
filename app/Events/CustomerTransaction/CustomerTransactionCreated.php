<?php

namespace App\Events\CustomerTransaction;

use App\Models\CustomerTransaction\CustomerTransaction;
use Illuminate\Queue\SerializesModels;

/**
 * Class CustomerTransactionCreated.
 */
class CustomerTransactionCreated
{
    use SerializesModels;

    /**
     * @var
     */
    public $customerTransaction;

    /**
     * @param $customerTransaction
     */
    public function __construct(CustomerTransaction $customerTransaction)
    {
        $this->customerTransaction = $customerTransaction;
    }
}
