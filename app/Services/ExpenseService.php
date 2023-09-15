<?php

namespace App\Services;

use App\Events\Expense\ExpenseCreated;
use App\Events\Expense\ExpenseDeleted;
use App\Events\Expense\ExpenseDestroyed;
use App\Events\Expense\ExpenseRestored;
use App\Events\Expense\ExpenseStatusChanged;
use App\Events\Expense\ExpenseUpdated;
use App\Models\Expense\Expense;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class ExpenseService.
 */
class ExpenseService extends BaseService
{
    /*
     * ExpenseService constructor.
     *
     * @param  Expense  $expense
     */
    public function __construct(Expense $expense)
    {
        $this->model = $expense;
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
     * @return Expense
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): Expense
    {
        DB::beginTransaction();
        if($data['type'] === 1) {
            
            $creditAmount =  (float)(str_replace(',', '', $data['creditAmount']));
            $debitAmount = 0;

        } else {
            $debitAmount =  (float)(str_replace(',', '', $data['debitAmount']));
            $creditAmount = 0;
        }
        try {
            $expense = $this->createExpense([
                'name' => $data['name'],
                'type' => $data['type'],
                'creditAmount' => $creditAmount,
                'debitAmount' => $debitAmount,
                'bank_log_id' => $data['bank_log_id'] ?? null,
                'refNo' => $data['refNo'] ?? null,
                'fund_category_id' => $data['fund_category_id']??null,
                'note' => $data['note'],
                'user_id' => $data['user_id'],
                'active' => 1,
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this expense. Please try again.'));
        }

        event(new ExpenseCreated($expense));

        DB::commit();

        return $expense;
    }

    /**
     * @param  array  $data
     * @return Expense
     */
    protected function createExpense(array $data = []): Expense
    {
        return $this->model::create([
            "name" => $data['name'],
            "type" => $data['type'],
            "creditAmount" => $data['creditAmount'],
            "debitAmount" => $data['debitAmount'],
            'bank_log_id' => $data['bank_log_id'],
            'refNo' => $data['refNo'],
            "note" => $data['note'],
            "active" => $data['active'],
            'user_id' => $data['user_id'],
            'fund_category_id' => $data['fund_category_id']??null,
        ]);
    }
}
