<?php

namespace App\Services;

use App\Events\CustomerTransaction\CustomerTransactionCreated;
use App\Events\CustomerTransaction\CustomerTransactionDeleted;
use App\Events\CustomerTransaction\CustomerTransactionDestroyed;
use App\Events\CustomerTransaction\CustomerTransactionRestored;
use App\Events\CustomerTransaction\CustomerTransactionStatusChanged;
use App\Events\CustomerTransaction\CustomerTransactionUpdated;
use App\Events\CustomerTransaction\CustomerTransactionVerify;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Exceptions\GeneralException;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\Pos\Pos;
use App\Models\CustomerTransactionDetail\CustomerTransactionDetail;
use App\Models\FundTransaction\FundTransaction;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Google\Service\Monitoring\Custom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class CustomerTransactionService.
 */
class CustomerTransactionService extends BaseService
{
    /*
     * CustomerTransactionService constructor.
     *
     * @param  CustomerTransaction  $customerTransaction
     */
    public function __construct(CustomerTransaction $customerTransaction)
    {
        $this->model = $customerTransaction;
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
     * @return CustomerTransaction
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): CustomerTransaction
    {
        DB::beginTransaction();
        $money =  -(float)(str_replace(',', '', $data['money']));
        $customer = Customer::find($data['customer_id']);
        $money_before = $customer->money;
        $money_after = $customer->money + $money;
        try {
            $customerTransaction = $this->createCustomerTransaction([
                "customer_id" => $data['customer_id'],
                "key" => $data['key'],
                "source" => $data['source'],
                "bank_id" => $data['bank_id'],
                "bank_code" => $data['bank_code'],
                "bank_customer_name" => $data['bank_customer_name'],
                "content" => $data['content'],
                "money" =>$money,
                "note" => $data['note'],
                'money_before' => $money_before,
                'money_after' => $money_after,
                "postingDate" => $data['postingDate'] ?? NULL,
                "transactionDate" => $data['transactionDate'] ?? NULL,
                "refNo" => $data['refNo'] ?? NULL,
                "isBankChecked" => $data['isBankChecked'] ?? 0,
                "bank_log_id" => $data['bank_log_id'] ?? NULL,

            ]);
            $customer->money = $money_after;
            $customer->save();

            // $data['name'] = 'Chuyển khoản cho user: ;'.$customer->id;
            // $data['type'] = 2;
            // $data['note'] = '<a href="'.route('admin.customerTransaction.show',['customerTransaction'=>$customerTransaction->id]).'">URL</a>';
            // $data['fund_category_id'] = 5;// Customer rút tiền
            // $data['debitAmount'] = -$customerTransaction->money;
            
            // $fundTransaction =  new FundTransaction();
            // $fundTransactionService = new FundTransactionService($fundTransaction);
            // $fundTransactionService->store($data);

            TelegramService::sendMessageCustomerTransaction($customerTransaction);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this customerTransaction. Please try again.'));
        }
        event(new CustomerTransactionCreated($customerTransaction));
        DB::commit();
        return $customerTransaction;
    }

    /**
     * @param  CustomerTransaction  $customerTransaction
     * @param  array  $data
     * @return CustomerTransaction
     *
     * @throws \Throwable
     */
    public function update(CustomerTransaction $customerTransaction, array $data = []): CustomerTransaction
    {
        throw new GeneralException(__('There was a problem updating this customerTransaction. Please try again.'));


        DB::beginTransaction();
        $money_customerTransaction =  (float)(str_replace(',', '', $data['money_customerTransaction']));
        $fee_customer =  (float)(str_replace(',', '', $data['fee_customer']));
        $fee_ship =  (float)(str_replace(',', '', $data['fee_ship']));
        $fee_user =  (float)(str_replace(',', '', $data['fee_user']));
        $fee_money_customer =  (($money_customerTransaction * $fee_customer/100))+ $fee_ship;
        $rest = $fee_money_customer % 1000; 
        if($rest > 0) 
        { 
            $fee_money_customer = $fee_money_customer - $rest + 1000; 
        } 

        if($data['transfer'] == 1) {
            $money_ = ($money_customerTransaction - ($money_customerTransaction * $fee_customer / 100)) - $fee_ship;
        } else {
            $money_ = $money_customerTransaction - $fee_money_customer;
        }
        $profit_money = $fee_money_customer - $fee_user;
        
        try {
                $customerTransaction->update([
                    'name' => $data['name'],
                    'money' => round($money_),
                    'transfer' => isset($data['transfer']) && $data['transfer'] === '1',
                    'user_fee_id' => $data['user_fee_id'],
                    'money_customerTransaction' => $money_customerTransaction,
                    'fee_customer' => $fee_customer,
                    'fee_ship' => $fee_ship,
                    'fee_user' => $fee_user,
                    'fee_money_customer' => $fee_money_customer,
                    'note' => $data['note'],
                    'active' => isset($data['active']) && $data['active'] === '1',
                    'profit_money' => $profit_money,
                    'bank_id' => $data['bank_id'],
                    'bank_code' => $data['bank_code'],
                    'bank_customer_name' => stripVN($data['bank_customer_name'])
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this customerTransaction. Please try again.'));
        }
        if(Auth::user()->checkRole(['admin','manager','manager_vip'])) {

        $customerTransaction->customerTransactionDetail()->delete();
        foreach($data['money']??[] as $k => $i) {
            $customerTransactionDetail = new CustomerTransactionDetail();
            $customerTransactionDetail->money = (float)(str_replace(',', '', $i));
            $customerTransactionDetail->bank = 'STK';
            $customerTransactionDetail->time = Carbon::now();
            $customerTransactionDetail->user_id = Auth::user()->id;
            $customerTransaction->customerTransactionDetail()->save($customerTransactionDetail);
        };
        }
        event(new CustomerTransactionUpdated($customerTransaction));

        DB::commit();
        return $customerTransaction;
    }


    /**
     * @param  CustomerTransaction  $customerTransaction
     * @param $status
     * @return CustomerTransaction
     *
     * @throws GeneralException
     */
    public function mark(CustomerTransaction $customerTransaction, $status): CustomerTransaction
    {
        $customerTransaction->active = $status;

        if ($customerTransaction->save()) {
            event(new CustomerTransactionStatusChanged($customerTransaction, $status));

            return $customerTransaction;
        }

        throw new GeneralException(__('There was a problem updating this customerTransaction. Please try again.'));
    }

    public function verify(CustomerTransaction $customerTransaction, $verify): CustomerTransaction
    {
        $money = $customerTransaction->customerTransactionDetail->sum('money');
        if(ceil($money/1000)*1000 != $customerTransaction->money) {
            throw new GeneralException(__('Số tiền chuyển cho khách và số tiền cần chuyển chưa khớp'));
        }
        $customerTransaction->isDone = $verify;

        if ($customerTransaction->save()) {
            event(new CustomerTransactionVerify($customerTransaction, $verify));

            return $customerTransaction;
        }

        throw new GeneralException(__('There was a problem updating this customerTransaction. Please try again.'));
    }
    /**
     * @param  CustomerTransaction  $customerTransaction
     * @return CustomerTransaction
     *
     * @throws GeneralException
     */
    public function delete(CustomerTransaction $customerTransaction): CustomerTransaction
    {

        if ($this->deleteById($customerTransaction->id)) {
            event(new CustomerTransactionDeleted($customerTransaction));

            return $customerTransaction;
        }

        throw new GeneralException('There was a problem deleting this customerTransaction. Please try again.');
    }

    /**
     * @param  CustomerTransaction  $customerTransaction
     * @return CustomerTransaction
     *
     * @throws GeneralException
     */
    public function restore(CustomerTransaction $customerTransaction): CustomerTransaction
    {
        if ($customerTransaction->restore()) {
            event(new CustomerTransactionRestored($customerTransaction));

            return $customerTransaction;
        }

        throw new GeneralException(__('There was a problem restoring this customerTransaction. Please try again.'));
    }

    /**
     * @param  CustomerTransaction  $customerTransaction
     * @return bool
     *
     * @throws GeneralException
     */
    public function destroy(CustomerTransaction $customerTransaction): bool
    {
        if ($customerTransaction->forceDelete()) {
            event(new CustomerTransactionDestroyed($customerTransaction));

            return true;
        }

        throw new GeneralException(__('There was a problem permanently deleting this customerTransaction. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return CustomerTransaction
     */
    protected function createCustomerTransaction(array $data = []): CustomerTransaction
    {
        return $this->model::create([
            "customer_id" => $data['customer_id'],
            "key" => $data['key'],
            "source" => $data['source'],
            "bank_id" => $data['bank_id'],
            "bank_code" => $data['bank_code'],
            "bank_customer_name" => $data['bank_customer_name'],
            "content" => $data['content'],
            "money" => $data['money'],
            "note" => $data['note'],
            'money_before' => $data['money_before'],
            'money_after' => $data['money_after'],
            "postingDate" => $data['postingDate'] ?? NULL,
            "transactionDate" => $data['transactionDate'] ?? NULL,
            "refNo" => $data['refNo'] ?? NULL,
            "isBankChecked" => $data['isBankChecked'] ?? 0,
            "bank_log_id" => $data['bank_log_id'] ?? NULL,
        ]);
    }
}
