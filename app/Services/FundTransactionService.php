<?php

namespace App\Services;

use App\Events\FundTransaction\FundTransactionCreated;
use App\Events\FundTransaction\FundTransactionDeleted;
use App\Events\FundTransaction\FundTransactionDestroyed;
use App\Events\FundTransaction\FundTransactionRestored;
use App\Events\FundTransaction\FundTransactionStatusChanged;
use App\Events\FundTransaction\FundTransactionUpdated;
use App\Models\FundTransaction\FundTransaction;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class FundTransactionService.
 */
class FundTransactionService extends BaseService
{
    /*
     * FundTransactionService constructor.
     *
     * @param  FundTransaction  $fundTransaction
     */
    public function __construct(FundTransaction $fundTransaction)
    {
        $this->model = $fundTransaction;
    }

    /**
     * @param $type
     * @param  bool|int  $perPage
     * @return mixed
     */
    public function getByType($type, $perPage = false)
    {
        if (is_numeric($perPage)) {
            return $this->model::byType($type)->paginate($perPage);
        }

        return $this->model::byType($type)->get();
    }


    /**
     * @param  array  $data
     * @return FundTransaction
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): FundTransaction
    {
        DB::beginTransaction();
        $lastest = FundTransaction::where('user_id',$data['user_id'])->latest('id')->first();

        if($lastest) {
            $money_before = $lastest->money_after ?? 0;
        }else {
            $money_before = 0;
        }
        if($data['type'] === 1) {
            
            $creditAmount =  (float)(str_replace(',', '', $data['creditAmount']));
            $debitAmount = 0;
            $money_after = $money_before + $creditAmount;

        } else {
            $debitAmount =  (float)(str_replace(',', '', $data['debitAmount']));
            $creditAmount = 0;
            $money_after = $money_before - $debitAmount;
        }
        try {
            $fundTransaction = $this->createFundTransaction([
                'name' => $data['name'],
                'type' => $data['type'],
                'creditAmount' => $creditAmount,
                'debitAmount' => $debitAmount,
                'money_before' => $money_before,
                'money_after' => $money_after,
                'bank_log_id' => $data['bank_log_id'] ?? null,
                'refNo' => $data['refNo'] ?? null,
                'fund_category_id' => $data['fund_category_id']??null,
                'note' => $data['note'],
                'user_id' => $data['user_id'],
                'active' => 1,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this fundTransaction. Please try again.'));
        }

        event(new FundTransactionCreated($fundTransaction));

        DB::commit();

        return $fundTransaction;
    }

    /**
     * @param  array  $data
     * @return FundTransaction
     */
    protected function createFundTransaction(array $data = []): FundTransaction
    {
        return $this->model::create([
            "name" => $data['name'],
            "type" => $data['type'],
            "creditAmount" => $data['creditAmount'],
            "debitAmount" => $data['debitAmount'],
            "money_before" => $data['money_before'],
            "money_after" => $data['money_after'],
            'bank_log_id' => $data['bank_log_id'],
            'refNo' => $data['refNo'],
            "note" => $data['note'],
            "active" => $data['active'],
            'user_id' => $data['user_id'],
            'fund_category_id' => $data['fund_category_id']??null,
        ]);
    }
}
