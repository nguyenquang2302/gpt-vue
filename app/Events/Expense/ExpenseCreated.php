<?php

namespace App\Events\Expense;

use App\Models\Expense\Expense;
use App\Services\TelegramService;
use Illuminate\Queue\SerializesModels;

/**
 * Class ExpenseCreated.
 */
class ExpenseCreated
{
    use SerializesModels;

    /**
     * @var
     */
    public $Expense;

    /**
     * @param $Expense
     */
    public function __construct(Expense $Expense)
    {
        $this->Expense = $Expense;
    }
}
