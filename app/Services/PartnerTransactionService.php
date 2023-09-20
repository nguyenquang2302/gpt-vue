<?php

namespace App\Services;

use App\Events\PartNerTransaction\PartNerTransactionCreated;
use App\Models\PartNerTransaction\PartNerTransaction;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class PartNerTransactionService.
 */
class PartnerTransactionService extends BaseService
{
    /*
     * PartNerTransactionService constructor.
     *
     * @param  PartNerTransaction  $fundTransaction
     */
    public function __construct(PartNerTransaction $fundTransaction)
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
     * @return PartNerTransaction
     *
     * @throws Exception
     * @throws \Throwable
     */
    public function store(array $data = []): PartNerTransaction
    {
        DB::beginTransaction();
        $lastest = PartNerTransaction::where('user_id',$data['user_id'])->latest('id')->first();

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
            $fundTransaction = $this->createPartNerTransaction([
                'name' => $data['name'],
                'type' => $data['type'],
                'creditAmount' => $creditAmount,
                'debitAmount' => $debitAmount,
                'money_before' => $money_before,
                'money_after' => $money_after,
                'refNo' => $data['refNo'] ?? null,
                'note' => $data['note'],
                'user_id' => $data['user_id'],
                'active' => 1,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception(__('There was a problem creating this fundTransaction. Please try again.'));
        }

        event(new PartNerTransactionCreated($fundTransaction));

        DB::commit();

        return $fundTransaction;
    }

    /**
     * @param  array  $data
     * @return PartNerTransaction
     */
    protected function createPartNerTransaction(array $data = []): PartNerTransaction
    {
        return $this->model::create([
            "name" => $data['name'],
            "type" => $data['type'],
            "creditAmount" => $data['creditAmount'],
            "debitAmount" => $data['debitAmount'],
            "money_before" => $data['money_before'],
            "money_after" => $data['money_after'],
            'refNo' => $data['refNo'],
            "note" => $data['note'],
            "active" => $data['active'],
            'user_id' => $data['user_id'],
        ]);
    }
}
