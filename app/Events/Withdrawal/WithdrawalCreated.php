<?php

namespace App\Events\Withdrawal;

use App\Models\Withdrawal\Withdrawal;
use App\Services\TelegramService;
use Illuminate\Queue\SerializesModels;

/**
 * Class WithdrawalCreated.
 */
class WithdrawalCreated
{
    use SerializesModels;

    /**
     * @var
     */
    public $withdrawal;

    /**
     * @param $withdrawal
     */
    public function __construct(Withdrawal $withdrawal)
    {
        $this->withdrawal = $withdrawal;
        TelegramService::sendMessageCreateDH($withdrawal);
    }
}
