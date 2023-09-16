<?php

namespace App\Events\Bank;

use App\Models\Bank\Bank;
use Illuminate\Queue\SerializesModels;

/**
 * Class BankStatusChanged.
 */
class BankStatusChanged
{
    use SerializesModels;

    /**
     * @var
     */
    public $bank;

    /**
     * @var
     */
    public $status;

    /**
     * BankStatusChanged constructor.
     *
     * @param  Bank  $bank
     * @param $status
     */
    public function __construct(Bank $bank, $status)
    {
        $this->bank = $bank;
        $this->status = $status;
    }
}
