<?php

namespace App\Events\Bank;

use App\Models\Bank\Bank;
use Illuminate\Queue\SerializesModels;

/**
 * Class BankDeleted.
 */
class BankDeleted
{
    use SerializesModels;

    /**
     * @var
     */
    public $bank;

    /**
     * @param $bank
     */
    public function __construct(Bank $bank)
    {
        $this->bank = $bank;
    }
}