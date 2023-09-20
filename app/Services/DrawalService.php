<?php

namespace App\Services;

use App\Events\Drawal\DrawalCreated;
use App\Events\Drawal\DrawalDeleted;
use App\Events\Drawal\DrawalDestroyed;
use App\Events\Drawal\DrawalRestored;
use App\Events\Drawal\DrawalStatusChanged;
use App\Events\Drawal\DrawalUpdated;
use App\Events\Drawal\DrawalVerify;
use App\Models\Drawal\Drawal;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Pos\Pos;
use App\Models\DrawalDetail\DrawalDetail;
use App\Models\PartnerTransaction\PartnerTransaction;
use App\Models\PosConsignment\PosConsignment;
use App\Models\Users\User;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Google\Service\Monitoring\Custom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class DrawalService.
 */
class DrawalService extends BaseService
{
    /*
     * DrawalService constructor.
     *
     * @param  Drawal  $drawal
     */
    public function __construct(Drawal $drawal)
    {
        $this->model = $drawal;
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
     * @return Drawal
     *
     * @throws Exception
     * @throws \Throwable
     */
    public function store(array $data = []): Drawal
    {
        DB::beginTransaction();
        $money_drawal =  (float)(str_replace(',', '', $data['money_drawal']));
        $fee_customer =  (float)(str_replace(',', '', $data['fee_customer']));
        $fee_ship =  (float)(str_replace(',', '', $data['fee_ship']));
        $fee_user =  (float)(str_replace(',', '', $data['fee_user']));
        $fee_money_customer =  (($money_drawal * $fee_customer / 100)) + $fee_ship;
        $rest = $fee_money_customer % 1000;
        if ($rest > 500) {
            $fee_money_customer = $fee_money_customer - $rest + 1000;
        } else {
            $fee_money_customer = $fee_money_customer - $rest;
        }
        if ($data['transfer'] == 1) {
            $money = ($money_drawal - $fee_money_customer) - $fee_ship;
            $profit_money = $money_drawal - $money - $fee_user;
        } else {

            $money = $money_drawal - $fee_money_customer;
            $fee_money_customer =  $fee_money_customer + $fee_ship;
            $profit_money = $fee_money_customer - $fee_user;
        }
        $branch_id = Auth::user()->branch_id;
        
        try {
            $drawal = $this->createDrawal([
                'name' => $data['name'],
                'transfer' => isset($data['transfer']) && $data['transfer'] === '1',
                'user_fee_id' => $data['user_fee_id'],
                'money_drawal' => $money_drawal,
                'money' => round($money),
                'fee_customer' => $fee_customer,
                'fee_ship' => $fee_ship,
                'fee_user' => $fee_user,
                'fee_money_customer' => $fee_money_customer,
                'note' => $data['note'],
                'active' => isset($data['active']) && $data['active'] === '1',
                'user_id' => Auth::user()->id,
                'profit_money' => $profit_money,
                'bank_id' => $data['bank_id'],
                'bank_code' => $data['bank_code'],
                'bank_customer_name' => stripVN($data['bank_customer_name']),
                'customer_id' => $data['customer_id'],
                'datetime' => Carbon::parse(($data['datetime']),'utc')->setTimezone(config('app.timezone')),
                'branch_id' => $branch_id,
                'stt' => $data['stt']

            ]);
            //  Kiểm tra tài khoản tạo 
            $customer = Customer::where('id',$data['customer_id'])->first();
            if($customer) {
                $userCreate = User::find($customer->user_id);
                //  Đối ứng
                if($userCreate->type == User::TYPE_PARTNER) {
                    $fee_partner = $userCreate->fee_partner;
                } else {
                    $fee_partner = $drawal->fee_customer;
                }
            }
            //  
            $drawal->drawalDetail()->delete();

                if ($data['details']) {
                    foreach ($data['details'] as $k => $detail) {
                        $group_bill = explode('.', $detail['group_bill']);
                        $group_bill[1] = ($group_bill[1]<100)?$group_bill[1]*10:$group_bill[1];
                        $group_bill[1] = ($group_bill[1]<10)?$group_bill[1]*100:$group_bill[1];
                        $pos = Pos::find($detail['pos_id']);
                        $drawalDetail = new DrawalDetail();
                        $drawalDetail->pos_id = $detail['pos_id'];
                        $drawalDetail->fee_bank = $pos->fee_bank;
                        $drawalDetail->money = (float)(str_replace(',', '', $detail['money']));
                        $drawalDetail->fee_bank_money = ($drawalDetail->money * $pos->fee_bank / 100);
                        $drawalDetail->customer_card_id = $detail['customer_card_id'];
                        $drawalDetail->money_back = $drawalDetail->money - $drawalDetail->fee_bank_money;
                        $drawalDetail->time = Carbon::now();
                        $drawalDetail->user_id = Auth::user()->id;
                        $drawalDetail->lo = $group_bill[0];
                        $drawalDetail->bill = $group_bill[1];
                        $drawalDetail->profit = ($drawalDetail->money * $drawal->fee_customer / 100) - ($drawalDetail->money * $drawalDetail->fee_bank / 100);
                        $drawalDetail->branch_id = $branch_id;
                        // phí đối ứng
                        $drawalDetail->fee_partner = $fee_partner;
                        $fee_partner_money = ($drawal->fee_customer - $fee_partner)*$drawalDetail->money/100;
                        $drawalDetail->fee_partner_money = $fee_partner_money;
                        $drawalDetail->user_partner_id = $userCreate?$userCreate->id:null;
                        $drawalDetail->profit = $drawalDetail->profit - $fee_partner_money;
                        // profit trừ phí cho đối ứng
                        $drawal->drawalDetail()->save($drawalDetail);

                    };
                }
            $drawal->profit = $drawal->caclulatorProfit();
            $drawal->save();
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception(__('There was a problem creating this drawal. Please try again.'));
        }
        event(new DrawalCreated($drawal));
        DB::commit();
        return $drawal;
    }

    /**
     * @param  Drawal  $drawal
     * @param  array  $data
     * @return Drawal
     *
     * @throws \Throwable
     */
    public function update(Drawal $drawal, array $data = []): Drawal
    {

        DB::beginTransaction();
        $money_drawal =  (float)(str_replace(',', '', $data['money_drawal']));
        $fee_customer =  (float)(str_replace(',', '', $data['fee_customer']));
        $fee_ship =  (float)(str_replace(',', '', $data['fee_ship']));
        $fee_user =  (float)(str_replace(',', '', $data['fee_user']));
        $fee_money_customer =  (($money_drawal * $fee_customer / 100)) + $fee_ship;
        $rest = $fee_money_customer % 1000;
        if ($rest > 500) {
            $fee_money_customer = $fee_money_customer - $rest + 1000;
        } else {
            $fee_money_customer = $fee_money_customer - $rest;
        }

        if ($data['transfer'] == 1) {
            $money_ = ($money_drawal - $fee_money_customer) - $fee_ship;
        } else {
            $money_ = $money_drawal - $fee_money_customer;
        }
        $profit_money = $fee_money_customer - $fee_user;


        try {
            $drawal->update([
                'name' => $data['name'],
                'money' => round($money_),
                'transfer' => isset($data['transfer']) && $data['transfer'] === '1',
                'user_fee_id' => $data['user_fee_id'],
                'money_drawal' => $money_drawal,
                'fee_customer' => $fee_customer,
                'fee_ship' => $fee_ship,
                'fee_user' => $fee_user,
                'fee_money_customer' => $fee_money_customer,
                'note' => $data['note'],
                'active' => isset($data['active']) && $data['active'] === '1',
                'profit_money' => $profit_money,
                'bank_code' => $data['bank_code'],
                'bank_customer_name' => stripVN($data['bank_customer_name']),
                'datetime' => Carbon::parse(($data['datetime']),'utc')->setTimezone(config('app.timezone')),
                'stt' => $data['stt']

            ]);
            if (Auth::user()->checkRole(['admin', 'manager', 'mod'])) {
                $customer = Customer::where('id',$drawal->customer_id)->first();
                if($customer) {
                    $userCreate = User::find($customer->user_id);
                    //  Đối ứng
                    if($userCreate->type == User::TYPE_PARTNER) {
                        $fee_partner = $userCreate->fee_partner;
                    } else {
                        $fee_partner = $drawal->fee_customer;
                    }
                }

                $drawal->drawalDetail()->delete();
                if ($data['details']) {
                    foreach ($data['details'] as $k => $detail) {
                        
                        $group_bill = explode('.', $detail['group_bill']);
                        $group_bill[1] = ($group_bill[1]<100)?$group_bill[1]*10:$group_bill[1];
                        $group_bill[1] = ($group_bill[1]<10)?$group_bill[1]*100:$group_bill[1];
                        $pos = Pos::find($detail['pos_id']);
                        $drawalDetail = new DrawalDetail();
                        $drawalDetail->pos_id = $detail['pos_id'];
                        $drawalDetail->fee_bank = $pos->fee_bank;
                        $drawalDetail->money = (float)(str_replace(',', '', $detail['money']));
                        $drawalDetail->fee_bank_money = ($drawalDetail->money * $pos->fee_bank / 100);
                        $drawalDetail->customer_card_id = $detail['customer_card_id'];
                        $drawalDetail->money_back = $drawalDetail->money - $drawalDetail->fee_bank_money;
                        $drawalDetail->time = Carbon::now();
                        $drawalDetail->user_id = Auth::user()->id;
                        $drawalDetail->lo = $group_bill[0];
                        $drawalDetail->bill = $group_bill[1];
                        $drawalDetail->profit = ($drawalDetail->money * $drawal->fee_customer / 100) - ($drawalDetail->money * $drawalDetail->fee_bank / 100);
                        $drawalDetail->branch_id = $drawal->branch_id;

                        // phí đối ứng
                        $drawalDetail->fee_partner = $fee_partner;
                        $fee_partner_money = ($drawal->fee_customer - $fee_partner)*$drawalDetail->money/100;
                        $drawalDetail->fee_partner_money = $fee_partner_money;
                        $drawalDetail->user_partner_id = $userCreate?$userCreate->id:null;
                        $drawalDetail->profit = $drawalDetail->profit - $fee_partner_money;
                        
                        $drawal->drawalDetail()->save($drawalDetail);
                    };
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception(__('There was a problem updating this drawal. Please try again.'));
        }
        $drawal->profit = $drawal->caclulatorProfit();
        $drawal->save();
        event(new DrawalUpdated($drawal));

        DB::commit();

        return $drawal;
    }


    /**
     * @param  Drawal  $drawal
     * @param $status
     * @return Drawal
     *
     * @throws Exception
     */
    public function mark(Drawal $drawal, $status): Drawal
    {
        $drawal->status = $status;

        if ($drawal->save()) {
            event(new DrawalStatusChanged($drawal, $status));

            return $drawal;
        }

        throw new Exception(__('There was a problem updating this drawal. Please try again.'));
    }

    public function verify(Drawal $drawal, $verify): Drawal
    {
        if ($drawal->isDone) {
            throw new Exception(__('Giao dịch đã được xác nhận'));
        }
        DB::beginTransaction();
        try {
            $money = $drawal->drawalDetail->sum('money');
            if ($money != $drawal->money_drawal) {
                throw new Exception(__('Số cần rút và số tiền rút chưa khớp'));
            }
            $total_partner_profilt = 0 ;
            foreach ($drawal->drawalDetail as $drawalDetail) {
                $total_partner_profilt = $total_partner_profilt + $drawalDetail->fee_partner_money;
                $drawalDetail->lo = $drawal->datetime->format('y_m_d_') . $drawalDetail->lo;
                $drawalDetail->save();
                if (!PosConsignment::where('pos_id', $drawalDetail->pos_id)->where('lo', $drawalDetail->lo)->first()) {
                    $posConsignment = new PosConsignment();
                    $posConsignment->lo = $drawalDetail->lo;
                    $posConsignment->pos_id = $drawalDetail->pos_id;
                    if ($drawalDetail->lo === '' || $drawalDetail->lo === NULL || !$drawalDetail->bill) {
                        DB::rollBack();
                        throw new Exception(__('Chưa nhập đúng số bill / lô'));
                    }
                    $posConsignment->save();
                }
            }

            $drawal->isDone = $verify;

            if ($drawal->save()) {

                $customer = Customer::find($drawal->customer_id);

                $money_before = $customer->money;

                $money_change =  $drawal->money;

                $money_after = $customer->money + $drawal->money;

                $customerTransaction = ([
                    "customer_id" => $drawal->customer_id,
                    "key" => $drawal->id,
                    "source" => 'RT',
                    "bank_id" => null,
                    "bank_code" => '',
                    "bank_customer_name" => '',
                    "content" => 'RT ' . $drawal->id . ' ' . $drawal->customer_id,
                    "money" => $money_change,
                    "note" => 'Giao dịch RT' . $drawal->id,
                    'money_before' => $money_before,
                    'money_after' => $money_after,
                    'isBankChecked' => 1,
                ]);

                $customerTransaction =  new CustomerTransaction($customerTransaction);
                $customerTransaction->save();
                $customer->money = $money_after;
                $customer->last_transaction_time = Carbon::now();
                $customer->save();
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
            }
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception(__('There was a problem update this drawal. Please try again.'));
        }
        event(new DrawalVerify($drawal));
        DB::commit();
        return $drawal;
    }
    /**
     * @param  Drawal  $drawal
     * @return Drawal
     *
     * @throws Exception
     */
    public function delete(Drawal $drawal): Drawal
    {

        if ($this->deleteById($drawal->id)) {

            if ($drawal->save()) {

                if ($drawal->isDone) {

                    $customer = Customer::find($drawal->customer_id);
                    $money_before = $customer->money;

                    $money_change =  -$drawal->money;

                    $money_after = $customer->money + $money_change;

                    $customerTransaction = ([
                        "customer_id" => $drawal->customer_id,
                        "key" => $drawal->id,
                        "source" => 'HTRT',
                        "bank_id" => null,
                        "bank_code" => '',
                        "bank_customer_name" => '',
                        "content" => 'HTRT ' . $drawal->id . ' ' . $drawal->customer_id,
                        "money" => $money_change,
                        "note" => 'HOÀN TIỀN Giao dịch RT' . $drawal->id,
                        'money_before' => $money_before,
                        'money_after' => $money_after,
                        'isBankChecked' => 1,
                    ]);
                    $customerTransaction =  new CustomerTransaction($customerTransaction);
                    $customerTransaction->save();
                    $customer->money = $money_after;
                    $customer->save();

                
                    $user = $customer->user;
                    if($user && $user->type == User::TYPE_PARTNER) {
                        $total_partner_profilt = 0;
                        foreach ($drawal->drawalDetail as $drawalDetail) {
                            $total_partner_profilt = $total_partner_profilt + $drawalDetail->fee_partner_money;
                        }
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
            }

            event(new DrawalDeleted($drawal));

            return $drawal;
        }

        throw new Exception('There was a problem deleting this drawal. Please try again.');
    }

    /**
     * @param  Drawal  $drawal
     * @return Drawal
     *
     * @throws Exception
     */
    public function reDone(Drawal $drawal): Drawal
    {

        if ($drawal->isDone) {

            $details = $drawal->drawalDetail;
            $total_partner_profilt = 0;
            foreach($details as $detail) {
                $total_partner_profilt = $total_partner_profilt + $detail->fee_partner_money;
                $detail->lo = substr($detail->lo,9,strlen($detail->lo)-9);
                $detail->save();
            }
            $customer = Customer::find($drawal->customer_id);
            $money_before = $customer->money;

            $money_change =  -$drawal->money;

            $money_after = $customer->money + $money_change;

            $customerTransaction = ([
                "customer_id" => $drawal->customer_id,
                "key" => $drawal->id,
                "source" => 'HTRT',
                "bank_id" => null,
                "bank_code" => '',
                "bank_customer_name" => '',
                "content" => 'HTRT ' . $drawal->id . ' ' . $drawal->customer_id,
                "money" => $money_change,
                "note" => 'HOÀN TIỀN Giao dịch RT' . $drawal->id,
                'money_before' => $money_before,
                'money_after' => $money_after,
                'isBankChecked' => 1,
            ]);
            $customerTransaction =  new CustomerTransaction($customerTransaction);
            $customerTransaction->save();
            $customer->money = $money_after;
            $customer->save();
            $drawal->isDone = false;
            $drawal->save();
            
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

        return $drawal;

        throw new Exception('There was a problem deleting this drawal. Please try again.');
    }


    /**
     * @param  Drawal  $drawal
     * @return Drawal
     *
     * @throws Exception
     */
    public function restore(Drawal $drawal): Drawal
    {
        if ($drawal->restore()) {
            event(new DrawalRestored($drawal));

            return $drawal;
        }

        throw new Exception(__('There was a problem restoring this drawal. Please try again.'));
    }

    /**
     * @param  Drawal  $drawal
     * @return bool
     *
     * @throws Exception
     */
    public function destroy(Drawal $drawal): bool
    {
        if ($drawal->forceDelete()) {
            event(new DrawalDestroyed($drawal));

            return true;
        }

        throw new Exception(__('There was a problem permanently deleting this drawal. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return Drawal
     */
    protected function createDrawal(array $data = []): Drawal
    {
        return $this->model::create([
            'name' => $data['name'],
            'transfer' => $data['transfer'],
            'user_fee_id' => $data['user_fee_id'],
            'money_drawal' => $data['money_drawal'],
            'money' => $data['money'],
            'fee_customer' => $data['fee_customer'],
            'fee_ship' => $data['fee_ship'],
            'fee_user' => $data['fee_user'],
            'fee_money_customer' => $data['fee_money_customer'],
            'note' => $data['note'],
            'active' => $data['active'],
            'user_id' => $data['user_id'],
            'customer_id' => $data['customer_id'],
            'profit_money' => $data['profit_money'],
            'bank_id' => $data['bank_id'],
            'bank_code' => $data['bank_code'],
            'bank_customer_name' => $data['bank_customer_name'],
            'datetime' => $data['datetime'],
            'branch_id' => $data['branch_id'],
            'stt' => $data['stt']
        ]);
    }
}
