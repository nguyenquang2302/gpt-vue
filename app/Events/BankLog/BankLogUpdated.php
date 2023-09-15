<?php

namespace App\Events\BankLog;

use App\Models\BankLog\BankLog;
use Illuminate\Queue\SerializesModels;

/**
 * Class BankUpdated.
 */
class BankLogUpdated
{
    use SerializesModels;

    /**
     * @var
     */
    public $bankLog;

    /**
     * @param $bankLog
     */
    public function __construct(BankLog $bankLog)
    {
        $this->bankLog = $bankLog;
    }
}
