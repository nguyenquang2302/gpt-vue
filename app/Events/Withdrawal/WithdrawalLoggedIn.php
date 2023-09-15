<?php

namespace App\Events\Withdrawal;

use App\Models\Withdrawal\Withdrawal;
use Illuminate\Queue\SerializesModels;

/**
 * Class WithdrawalLoggedIn.
 */
class WithdrawalLoggedIn
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
    }
}
