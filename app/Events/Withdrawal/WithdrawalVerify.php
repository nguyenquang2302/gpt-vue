<?php

namespace App\Events\Withdrawal;

use App\Models\FundTransaction\FundTransaction;
use App\Models\Withdrawal\Withdrawal;
use App\Services\FundTransactionService;
use App\Services\TelegramService;
use Illuminate\Queue\SerializesModels;

/**
 * Class WithdrawalUpdated.
 */
class WithdrawalVerify
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

        // $data['name'] = 'Nạp tiền đáo hạn ID:'.$withdrawal->id;
        // $data['type'] = 2;
        // $data['note'] = 'URL: '.route('admin.withdrawal.show',['withdrawal'=>$withdrawal]);
        // $data['fund_category_id'] = 4;// NTDH
        // $data['debitAmount'] = $withdrawal->withdrawalDetail()->sum('money');
        
        // $fundTransaction =  new FundTransaction();
        // $fundTransactionService = new FundTransactionService($fundTransaction);
        // $fundTransactionService->store($data);

        TelegramService::sendMessageConfirmDH($withdrawal);
    }
}
