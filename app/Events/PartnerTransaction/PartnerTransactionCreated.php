<?php

namespace App\Events\PartnerTransaction;

use App\Models\PartnerTransaction\PartnerTransaction;
use Illuminate\Queue\SerializesModels;

/**
 * Class PartnerTransactionCreated.
 */
class PartnerTransactionCreated
{
    use SerializesModels;

    /**
     * @var
     */
    public $PartnerTransaction;

    /**
     * @param $PartnerTransaction
     */
    public function __construct(PartnerTransaction $PartnerTransaction)
    {
        $this->PartnerTransaction = $PartnerTransaction;
    }
}
