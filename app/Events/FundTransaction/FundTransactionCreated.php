<?php

namespace App\Events\FundTransaction;

use App\Models\FundTransaction\FundTransaction;
use App\Services\TelegramService;
use Illuminate\Queue\SerializesModels;

/**
 * Class FundTransactionCreated.
 */
class FundTransactionCreated
{
    use SerializesModels;

    /**
     * @var
     */
    public $FundTransaction;

    /**
     * @param $FundTransaction
     */
    public function __construct(FundTransaction $FundTransaction)
    {
        $this->FundTransaction = $FundTransaction;
        TelegramService::sendMessageFundTransaction($FundTransaction);
    }
}
