<?php

namespace App\Services;

use App\Events\Withdrawal\WithdrawalCreated;
use App\Events\Withdrawal\WithdrawalDeleted;
use App\Events\Withdrawal\WithdrawalDestroyed;
use App\Events\Withdrawal\WithdrawalRestored;
use App\Events\Withdrawal\WithdrawalStatusChanged;
use App\Events\Withdrawal\WithdrawalUpdated;
use App\Events\Withdrawal\WithdrawalVerify;
use App\Models\Withdrawal\Withdrawal;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\PartnerTransaction\PartnerTransaction;
use App\Models\Pos\Pos;
use App\Models\PosConsignment\PosConsignment;
use App\Models\Users\User;
use App\Models\WithdrawalDetail\WithdrawalDetail;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Google\Service\Monitoring\Custom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class WithdrawalService.
 */
class WithdrawalService extends BaseService
{
    /*
     * WithdrawalService constructor.
     *
     * @param  Withdrawal  $withdrawal
     */
    public function __construct(Withdrawal $withdrawal)
    {
        $this->model = $withdrawal;
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
     * @return Withdrawal
     *
     * @throws \Throwable
     */
    public function store(array $data = []): Withdrawal
    {
        DB::beginTransaction();
        $money_withdrawal =  (float)(str_replace(',', '', $data['money_withdrawal']));
        $fee_customer =  (float)(str_replace(',', '', $data['fee_customer']));
        $fee_ship =  (float)(str_replace(',', '', $data['fee_ship']));
        $fee_user =  (float)(str_replace(',', '', $data['fee_user']));
        $fee_money_customer =  (($money_withdrawal * $fee_customer / 100)) + $fee_ship;
        $rest = $fee_money_customer % 1000;
        if ($rest > 500) {
            $fee_money_customer = $fee_money_customer - $rest + 1000;
        } else {
            $fee_money_customer = $fee_money_customer - $rest;
        }

        $CustomerCard = CustomerCard::find($data['customer_card_id']);
        $profit_money =  $fee_money_customer - $fee_user;
        $branch_id = Auth::user()->branch_id;
        try {
            $withdrawal = $this->createWithdrawal([
                'isOwnerPos' => $data['isOwnerPos'] ?? null,
                'name' => $data['name'],
                'user_fee_id' => $data['user_fee_id'],
                'money_withdrawal' => $money_withdrawal,
                'fee_customer' => $fee_customer,
                'fee_ship' => $fee_ship,
                'fee_user' => $fee_user,
                'fee_money_customer' => $fee_money_customer,
                'note' => $data['note'],
                'active' => isset($data['active']) && $data['active'] === '1',
                'user_id' => Auth::user()->id,
                'customer_card_id' => $data['customer_card_id'],
                'customer_id' => $CustomerCard->customer_id,
                'profit_money' => $profit_money,
                'datetime' => Carbon::parse(($data['datetime']), 'utc'),
                'branch_id' => $branch_id,
                'stt' => $data['stt']
            ]);

            if ($data['details']) {
                $customer = Customer::where('id', $CustomerCard->customer_id)->first();
                if ($customer) {
                    $userCreate = User::find($customer->user_id);
                    //  Đối ứng
                    if ($userCreate->type == User::TYPE_PARTNER) {
                        $fee_partner = $userCreate->fee_partner;
                    } else {
                        $fee_partner = $withdrawal->fee_customer;
                    }
                }

                foreach ($data['details'] ?? [] as $k => $detail) {
                    $detail['lo'] = (int)$detail['lo'];
                    if($detail['lo'] <10)
                    $detail['lo'] = ($detail['lo']<10)?'00'.$detail['lo']:$detail['lo'];
                    else
                    $detail['lo'] = ($detail['lo']<100)?'0'.$detail['lo']:$detail['lo'];
                    
                    $detail['bill'] = (int)$detail['bill'];

                    if((int)$detail['bill'] <10)
                    $detail['bill'] = ($detail['bill']<10)?'00'.$detail['bill']:$detail['bill'];
                    else if((int)$detail['bill'] <100)
                    $detail['bill'] = ($detail['bill']<100)?'0'.$detail['bill']:$detail['bill'];
                    
                    // if (!$group_bill[0] || !$group_bill[1]) {
                    // }
                    $pos = Pos::find($detail['pos_id']);
                    $withdrawalDetail = new WithdrawalDetail();
                    $withdrawalDetail->pos_id = $detail['pos_id'];
                    $withdrawalDetail->money = (float)(str_replace(',', '', $detail['money']));
                    $withdrawalDetail->money_drawal = (float)(str_replace(',', '', $detail['money_drawal']));
                    $withdrawalDetail->fee_bank = $pos->fee_bank;
                    $withdrawalDetail->time = Carbon::now();
                    $withdrawalDetail->user_id = Auth::user()->id;
                    $withdrawalDetail->fee_money_bank = ($withdrawalDetail->money_drawal * $pos->fee_bank / 100);
                    $withdrawalDetail->profit = ($withdrawalDetail->money_drawal * $withdrawal->fee_customer) / 100 - ($withdrawalDetail->money_drawal * $withdrawalDetail->fee_bank) / 100;
                    $withdrawalDetail->branch_id = $branch_id;
                    if (isset($detail['lo'])) {
                        $withdrawalDetail->lo = $detail['lo'];
                    }
                    if (isset($detail['bill'])) {
                        $withdrawalDetail->bill = $detail['bill'];
                    }
                    $withdrawalDetail->money_back = $withdrawalDetail->money_drawal - $withdrawalDetail->fee_money_bank;

                    // phí đối ứng
                    $withdrawalDetail->fee_partner = $fee_partner;
                    $fee_partner_money = ($withdrawal->fee_customer - $fee_partner) * $withdrawalDetail->money_drawal / 100;
                    $withdrawalDetail->fee_partner_money = $fee_partner_money;
                    $withdrawalDetail->user_partner_id = $userCreate ? $userCreate->id : null;
                    $withdrawalDetail->profit = $withdrawalDetail->profit - $fee_partner_money;

                    $withdrawal->withdrawalDetail()->save($withdrawalDetail);
                };
            }
        } catch (Exception $e) {
            DB::rollBack();
        }
        $withdrawal->profit = $withdrawal->caclulatorProfit();
        $withdrawal->save();
        event(new WithdrawalCreated($withdrawal));
        DB::commit();
        return $withdrawal;
    }

    /**
     * @param  Withdrawal  $withdrawal
     * @param  array  $data
     * @return Withdrawal
     *
     * @throws \Throwable
     */
    public function update(Withdrawal $withdrawal, array $data = []): Withdrawal
    {
        DB::beginTransaction();
        $money_withdrawal =  (float)(str_replace(',', '', $data['money_withdrawal']));
        $fee_customer =  (float)(str_replace(',', '', $data['fee_customer']));
        $fee_ship =  (float)(str_replace(',', '', $data['fee_ship']));
        $fee_user =  (float)(str_replace(',', '', $data['fee_user']));
        $fee_money_customer =  (($money_withdrawal * $fee_customer / 100)) + $fee_ship;
        $rest = $fee_money_customer % 1000;
        if ($rest > 500) {
            $fee_money_customer = $fee_money_customer - $rest + 1000;
        } else {
            $fee_money_customer = $fee_money_customer - $rest;
        }

        // $CustomerCard = CustomerCard::find($data['customer_card_id']);
        $profit_money =  $fee_money_customer - $fee_user;
        try {
            $withdrawal->update([
                'isOwnerPos' => $data['isOwnerPos'] ?? null,
                'name' => $data['name'],
                'user_fee_id' => $data['user_fee_id'],
                'money_withdrawal' => $money_withdrawal,
                'fee_customer' => $fee_customer,
                'fee_ship' => $fee_ship,
                'fee_user' => $fee_user,
                'fee_money_customer' => $fee_money_customer,
                'profit_money' => $profit_money,
                'note' => $data['note'],
                'datetime' => Carbon::parse(($data['datetime']), 'utc'),
                'stt' => $data['stt']

            ]);

            $withdrawal->withdrawalDetail()->delete();
            if ($data['details']) {
                $customer = Customer::where('id', $withdrawal->customerCard->customer_id)->first();
                if ($customer) {
                    $userCreate = User::find($customer->user_id);
                    //  Đối ứng
                    if ($userCreate->type == User::TYPE_PARTNER) {
                        $fee_partner = $userCreate->fee_partner;
                    } else {
                        $fee_partner = $withdrawal->fee_customer;
                    }
                }
                foreach ($data['details'] ?? [] as $k => $detail) {
                    $detail['lo'] = (int)$detail['lo'];
                    if($detail['lo'] <10)
                    $detail['lo'] = ($detail['lo']<10)?'00'.$detail['lo']:$detail['lo'];
                    else
                    $detail['lo'] = ($detail['lo']<100)?'0'.$detail['lo']:$detail['lo'];
                    
                    $detail['bill'] = (int)$detail['bill'];

                    if((int)$detail['bill'] <10)
                    $detail['bill'] = ($detail['bill']<10)?'00'.$detail['bill']:$detail['bill'];
                    else if((int)$detail['bill'] <100)
                    $detail['bill'] = ($detail['bill']<100)?'0'.$detail['bill']:$detail['bill'];
                    
                    $pos = Pos::find($detail['pos_id']);
                    $withdrawalDetail = new WithdrawalDetail();
                    $withdrawalDetail->pos_id = $detail['pos_id'];
                    $withdrawalDetail->money = (float)(str_replace(',', '', $detail['money']));
                    $withdrawalDetail->money_drawal = (float)(str_replace(',', '', $detail['money_drawal']));
                    $withdrawalDetail->fee_bank = $pos->fee_bank;

                    $withdrawalDetail->time = Carbon::now();
                    $withdrawalDetail->user_id = Auth::user()->id;
                    $withdrawalDetail->fee_money_bank = ($withdrawalDetail->money_drawal * $pos->fee_bank / 100);
                    $withdrawalDetail->profit = ($withdrawalDetail->money_drawal * $withdrawal->fee_customer) / 100 - ($withdrawalDetail->money_drawal * $withdrawalDetail->fee_bank) / 100;
                    $withdrawalDetail->branch_id = $withdrawal->branch_id;
                    if (isset($detail['lo'])) {
                        $withdrawalDetail->lo = $detail['lo'];
                    }
                    if (isset($detail['bill'])) {
                        $withdrawalDetail->bill = $detail['bill'];
                    }

                    $withdrawalDetail->money_back = $withdrawalDetail->money_drawal - $withdrawalDetail->fee_money_bank;

                    // phí đối ứng
                    $withdrawalDetail->fee_partner = $fee_partner;
                    $fee_partner_money = ($withdrawal->fee_customer - $fee_partner) * $withdrawalDetail->money_drawal / 100;
                    $withdrawalDetail->fee_partner_money = $fee_partner_money;
                    $withdrawalDetail->user_partner_id = $userCreate ? $userCreate->id : null;
                    $withdrawalDetail->profit = $withdrawalDetail->profit - $fee_partner_money;

                    $withdrawal->withdrawalDetail()->save($withdrawalDetail);
                };
            }
        } catch (Exception $e) {
            DB::rollBack();
        }
        $withdrawal->profit = $withdrawal->caclulatorProfit();
        $withdrawal->save();
        event(new WithdrawalUpdated($withdrawal));

        DB::commit();
        return $withdrawal;
    }


    /**
     * @param  Withdrawal  $withdrawal
     * @param $status
     * @return Withdrawal
     *
     */
    public function mark(Withdrawal $withdrawal, $status): Withdrawal
    {
        $withdrawal->status = $status;

        if ($withdrawal->save()) {
            event(new WithdrawalStatusChanged($withdrawal, $status));

            return $withdrawal;
        }
    }

    public function verify(Withdrawal $withdrawal, $verify): Withdrawal
    {
        DB::beginTransaction();
        if ($withdrawal->isDone) {
            return $withdrawal;
        }
        $details = $withdrawal->withdrawalDetail;
        $money  = $details->sum('money');
        $money_drawal  = $details->sum('money_drawal');
        $total_partner_profilt  = $details->sum('fee_partner_money');
        foreach ($withdrawal->withdrawalDetail as $drawalDetail) {
            $drawalDetail->lo = $withdrawal->datetime->format('y_m_d_') . $drawalDetail->lo;
            if ($withdrawal->isOwnerPos) {
                $drawalDetail->isOwnerPos = true;
            }
            $drawalDetail->save();
            if ($drawalDetail->money_drawal > 0) {
                if (!PosConsignment::where('pos_id', $drawalDetail->pos_id)->where('lo', $drawalDetail->lo)->first()) {
                    $posConsignment = new PosConsignment();
                    $posConsignment->lo = $drawalDetail->lo;
                    $posConsignment->pos_id = $drawalDetail->pos_id;
                    $posConsignment->save();
                    if (($drawalDetail->lo == 0 || !$drawalDetail->lo || !$drawalDetail->bill)) {
                        DB::rollBack();
                    }
                }
            }
        }
        $withdrawal->isDone = $verify;
        if ($withdrawal->save()) {
            $customer = Customer::find($withdrawal->customer_id);

            if ($withdrawal->fee_money_customer > 0) {

                $CustomerCard = $withdrawal->customerCard;
                $money_before = $customer->money;

                $money_change =  -$withdrawal->fee_money_customer;

                $money_after = $customer->money - $withdrawal->fee_money_customer;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'PDH',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'PDH ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Phí đáo hạn',
                    'money_before' => $money_before,
                    'money_after' => $money_after
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->last_transaction_time = Carbon::now();
                $customer->save();
            }
            if ($money > $money_drawal) {
                $money_before = $customer->money;
                $money_change =  - ($money - $money_drawal);
                $money_after = $customer->money + $money_change;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'NTDH',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'NTDHKR ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Nạp tiền đáo hạn không rút hết',
                    'money_before' => $money_before,
                    'money_after' => $money_after
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->last_transaction_time = Carbon::now();
                $customer->save();
            } elseif ($money < $money_drawal) {
                $money_before = $customer->money;
                $money_change   = ($money_drawal - $money) - ($money_drawal - $money) * ($withdrawal->fee_customer) / 100;
                $money_after = $customer->money + $money_change;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'DAOHANDU',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'NTDHKR ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'ĐÁO HẠN RÚT DƯ TIỀN',
                    'money_before' => $money_before,
                    'money_after' => $money_after
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->last_transaction_time = Carbon::now();
                $customer->save();
            }
            event(new WithdrawalVerify($withdrawal, $verify));


            $user = $customer->user;
                if($user && $user->type == User::TYPE_PARTNER) {
                    $data['name'] = 'Nhận phí đối ứng';
                    $data['type'] = 1;
                    $data['note'] = $customer->name;
                    $data['creditAmount'] = $total_partner_profilt;
                    $data['debitAmount'] = 0;
                    $data['user_id'] = $user->id;
                    $data['refNo'] = null;
                    $fundTransaction =  new PartnerTransaction();
                    $fundTransactionService = new PartnerTransactionService($fundTransaction);
                    $fundTransactionService->store($data);
                }
                
            DB::commit();
            return $withdrawal;
        }
    }
    /**
     * @param  Withdrawal  $withdrawal
     * @return Withdrawal
     *
     */
    public function delete(Withdrawal $withdrawal): Withdrawal
    {
        DB::beginTransaction();

        if ($withdrawal->isDone) {
            $details = $withdrawal->withdrawalDetail;
            $money  = $details->sum('money');
            $money_drawal  = $details->sum('money_drawal');
            $total_partner_profilt = $details->sum('fee_partner_money');
            if ($withdrawal->fee_money_customer > 0) {

                $CustomerCard = $withdrawal->customerCard;
                $customer = Customer::find($CustomerCard->customer_id);
                $money_before = $customer->money;

                $money_change =  $withdrawal->fee_money_customer;

                $money_after = $customer->money + $withdrawal->fee_money_customer;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'HTPDH',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'Hoàn tiền PDH ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Phí đáo hạn',
                    'money_before' => $money_before,
                    'money_after' => $money_after
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->save();
            }
            if ($money > $money_drawal) {
                $customer = Customer::find($withdrawal->customer_id);
                $money_before = $customer->money;
                $money_change =  ($money - $money_drawal);
                $money_after = $customer->money + $money_change;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'HTNTDH',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'Hoàn tiền NTDHKR ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Nạp tiền đáo hạn không rút hết',
                    'money_before' => $money_before,
                    'money_after' => $money_after
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->save();
            } elseif ($money < $money_drawal) {
                $customer = Customer::find($withdrawal->customer_id);

                $money_before = $customer->money;
                $money_change   = - (($money_drawal - $money) - ($money_drawal - $money) * ($withdrawal->fee_customer) / 100);
                $money_after = $customer->money + $money_change;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'HTDAOHANDU',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'Hoàn tiềnNTDHKR ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Hoàn tiền ĐÁO HẠN RÚT DƯ TIỀN',
                    'money_before' => $money_before,
                    'money_after' => $money_after,
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->save();
            }

            $user = $customer->user;
            if($user && $user->type == User::TYPE_PARTNER) {
                $data['name'] = 'Giao dịch đã Huỷ';
                $data['type'] = 2;
                $data['note'] = $customer->name;
                $data['creditAmount'] = 0;
                $data['debitAmount'] = $total_partner_profilt;
                $data['user_id'] = $user->id;
                $data['refNo'] = null;
                $fundTransaction =  new PartnerTransaction();
                $fundTransactionService = new PartnerTransactionService($fundTransaction);
                $fundTransactionService->store($data);
            }

        }

        if ($this->deleteById($withdrawal->id)) {
            event(new WithdrawalDeleted($withdrawal));
            DB::commit();
            return $withdrawal;
        }
        DB::rollBack();
    }

    /**
     * @param  Withdrawal  $withdrawal
     * @return Withdrawal
     *
     */
    public function restore(Withdrawal $withdrawal): Withdrawal
    {
        if ($withdrawal->restore()) {
            event(new WithdrawalRestored($withdrawal));

            return $withdrawal;
        }
    }

    /**
     * @param  Withdrawal  $withdrawal
     * @return bool
     *
     */
    public function destroy(Withdrawal $withdrawal): bool
    {
        if ($withdrawal->forceDelete()) {
            event(new WithdrawalDestroyed($withdrawal));

            return true;
        }
    }

    /**
     * @param  array  $data
     * @return Withdrawal
     */
    protected function createWithdrawal(array $data = []): Withdrawal
    {
        return $this->model::create([
            'isOwnerPos' => $data['isOwnerPos'] ?? 0,
            'name' => $data['name'],
            'user_fee_id' => $data['user_fee_id'],
            'money_withdrawal' => $data['money_withdrawal'],
            'fee_customer' => $data['fee_customer'],
            'fee_ship' => $data['fee_ship'],
            'fee_user' => $data['fee_user'],
            'fee_money_customer' => $data['fee_money_customer'],
            'note' => $data['note'],
            'active' => $data['active'],
            'user_id' =>  $data['user_id'],
            'customer_card_id' => $data['customer_card_id'],
            'customer_id' => $data['customer_id'],
            'profit_money' => $data['profit_money'],
            'datetime' => $data['datetime'],
            'branch_id' => $data['branch_id'],
            'stt' => $data['stt']

        ]);
    }

    public function reDone(Withdrawal $withdrawal): Withdrawal
    {
        // DB::beginTransaction();
        if ($withdrawal->isDone) {
            $details = $withdrawal->withdrawalDetail;
            $total_partner_profilt = 0;
            foreach ($details as $detail) {
                $total_partner_profilt = $total_partner_profilt + $detail->fee_partner_money;
                $detail->lo = substr($detail->lo, 9, strlen($detail->lo) - 9);
                $detail->save();
            }
            $money  = $details->sum('money');
            $money_drawal  = $details->sum('money_drawal');
            if ($withdrawal->fee_money_customer > 0) {

                $CustomerCard = $withdrawal->customerCard;
                $customer = Customer::find($CustomerCard->customer_id);
                $money_before = $customer->money;

                $money_change =  $withdrawal->fee_money_customer;

                $money_after = $customer->money + $withdrawal->fee_money_customer;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'HTPDH',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'Hoàn tiền PDH ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Phí đáo hạn',
                    'money_before' => $money_before,
                    'money_after' => $money_after
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->save();
            }
            if ($money > $money_drawal) {
                $customer = Customer::find($withdrawal->customer_id);
                $money_before = $customer->money;
                $money_change =  ($money - $money_drawal);
                $money_after = $customer->money + $money_change;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'HTNTDH',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'Hoàn tiền NTDHKR ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Nạp tiền đáo hạn không rút hết',
                    'money_before' => $money_before,
                    'money_after' => $money_after
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->save();
            } elseif ($money < $money_drawal) {
                $customer = Customer::find($withdrawal->customer_id);

                $money_before = $customer->money;
                $money_change   = - (($money_drawal - $money) - ($money_drawal - $money) * ($withdrawal->fee_customer) / 100);
                $money_after = $customer->money + $money_change;
                $customerTransaction = ([
                    "customer_id" => $withdrawal->customer_id,
                    "key" => $withdrawal->id,
                    "source" => 'HTDAOHANDU',
                    "bank_id" => config('bank.accId'),
                    "bank_code" => config('bank.accountNo'),
                    "bank_customer_name" => config('bank.accountName'),
                    "content" => 'Hoàn tiềnNTDHKR ' . $withdrawal->id . ' ' . $withdrawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Hoàn tiền ĐÁO HẠN RÚT DƯ TIỀN',
                    'money_before' => $money_before,
                    'money_after' => $money_after
                ]);
                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->save();
            }
        }
        $withdrawal->isDone = false;
        $withdrawal->save();
        $user = $customer->user;
        if($user && $user->type == User::TYPE_PARTNER) {
            $data['name'] = 'Giao dịch đã Huỷ';
            $data['type'] = 2;
            $data['note'] = $customer->name;
            $data['creditAmount'] = 0;
            $data['debitAmount'] = $total_partner_profilt;
            $data['user_id'] = $user->id;
            $data['refNo'] = null;
            $fundTransaction =  new PartnerTransaction();
            $fundTransactionService = new PartnerTransactionService($fundTransaction);
            $fundTransactionService->store($data);
        }

        event(new WithdrawalDeleted($withdrawal));
        DB::commit();
        return $withdrawal;
        DB::rollBack();
    }
}
