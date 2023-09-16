<?php

namespace App\Services;

use App\Events\Customer\CustomerCreated;
use App\Events\Customer\CustomerDeleted;
use App\Events\Customer\CustomerDestroyed;
use App\Events\Customer\CustomerRestored;
use App\Events\Customer\CustomerStatusChanged;
use App\Events\Customer\CustomerUpdated;
use App\Models\Customer\Customer;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class CustomerService.
 */
class CustomerService extends BaseService
{
    /*
     * CustomerService constructor.
     *
     * @param  Customer  $customer
     */
    public function __construct(Customer $customer)
    {
        $this->model = $customer;
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
     * @return Customer
     *
     * @throws Exception
     * @throws \Throwable
     */
    public function store(array $data = []): Customer
    {
        DB::beginTransaction();
        $birth_day  = Carbon::parse(($data['birth_day']),'utc')->setTimezone(config('app.timezone'));

        // try {
            $customer = $this->createCustomer([
                'name' => $data['name'],
                'email' => $data['email']??'',
                'cmnd' => $data['cmnd'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'birth_day' => $birth_day,
                'note' => $data['note']??'',
                'province_id' => $data['province_id']?? null,
                'district_id' => $data['district_id']?? null,
                'ward_id' => $data['ward_id']?? null,
                'type' => $data['type']?? null,
                'active' => isset($data['active']) && $data['active'] === '1',
                'user_id' => Auth::user()->id
            ]);
        // } catch (Exception $e) {
        //     DB::rollBack();

        //     throw new Exception(__('There was a problem creating this customer. Please try again.'));
        // }

        event(new CustomerCreated($customer));

        DB::commit();

        return $customer;
    }

    /**
     * @param  Customer  $customer
     * @param  array  $data
     * @return Customer
     *
     * @throws \Throwable
     */
    public function update(Customer $customer, array $data = []): Customer
    {
        DB::beginTransaction();
        $birth_day  = Carbon::parse(($data['birth_day']),'utc')->setTimezone(config('app.timezone'));
        try {
            $customer->update([
                'name' => $data['name'],
                'email' => $data['email'],
                'active' => $data['active'],
                'cmnd' => $data['cmnd'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'birth_day' => $birth_day,
                'note' => $data['note'],
                'province_id' => $data['province_id'],
                'district_id' => $data['district_id'],
                'ward_id' => $data['ward_id'],
                'type' => $data['type'],
                'active' => isset($data['active']) && $data['active'] === '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception(__('There was a problem updating this customer. Please try again.'));
        }

        event(new CustomerUpdated($customer));

        DB::commit();

        return $customer;
    }


    public function upVip(Customer $customer, array $data = []): Customer
    {
        if($customer->vip) {
            throw new Exception(__('KH đã là VIP'));
        }
        DB::beginTransaction();
        try {
            $customer->update([
                'vip' => $data['vip'],
                'introduced_by_user_id' => $data['introduced_by_user_id'],
                'request_vip' => 0,
                'vip_time' => Carbon::now(),
                'vip_time_expires' => Carbon::now()->endOfYear(),
            ]);

            $money_before = $customer->money;

            $money_change = -999000;

            $money_after = $customer->money - 999000;
            $customerTransaction = ([
                "customer_id" => $customer->id,
                "key" => $customer->id,
                "source" => 'VIP1',
                "bank_id" => config('bank.accId'),
                "bank_code" => config('bank.accountNo'),
                "bank_customer_name" => config('bank.accountName'),
                "content" => 'VIP1',
                "money" => $money_change,
                "note" => 'MUA VIP',
                'money_before' => $money_before,
                'money_after' => $money_after
            ]);
            $customerTransaction =  new CustomerTransaction($customerTransaction);
            $customerTransaction->save();
            $customer->money = $money_after;
            $customer->save();

        } catch (Exception $e) {
            DB::rollBack();

            throw new Exception(__('There was a problem updating this customer. Please try again.'));
        }

        event(new CustomerUpdated($customer));

        DB::commit();

        return $customer;
    }

    /**
     * @param  Customer  $customer
     * @param  array  $data
     * @return Customer
     */
    public function updateProfile(Customer $customer, array $data = []): Customer
    {
        $customer->name = $data['name'];
        $customer->email = $data['email'];
        $customer->active = $data['active'];
        $customer->cmnd = $data['cmnd'];
        $customer->phone = $data['phone'];
        $customer->address = $data['address'];
        $customer->birth_day = $data['birth_day'];
        $customer->note = $data['note'];
        $customer->province_id = $data['province_id'];
        $customer->district_id = $data['district_id'];
        $customer->ward_id = $data['ward_id'];
        $customer->active = isset($data['active']) && $data['active'] === '1';

        return tap($customer)->save();
    }

    /**
     * @param  Customer  $customer
     * @param $status
     * @return Customer
     *
     * @throws Exception
     */
    public function mark(Customer $customer, $status): Customer
    {
        $customer->active = $status;

        if ($customer->save()) {
            event(new CustomerStatusChanged($customer, $status));

            return $customer;
        }

        throw new Exception(__('There was a problem updating this customer. Please try again.'));
    }

    /**
     * @param  Customer  $customer
     * @return Customer
     *
     * @throws Exception
     */
    public function delete(Customer $customer): Customer
    {
        if ($customer->id === auth()->id()) {
            throw new Exception(__('You can not delete yourself.'));
        }

        if ($this->deleteById($customer->id)) {
            event(new CustomerDeleted($customer));

            return $customer;
        }

        throw new Exception('There was a problem deleting this customer. Please try again.');
    }

    /**
     * @param  Customer  $customer
     * @return Customer
     *
     * @throws Exception
     */
    public function restore(Customer $customer): Customer
    {
        if ($customer->restore()) {
            event(new CustomerRestored($customer));

            return $customer;
        }

        throw new Exception(__('There was a problem restoring this customer. Please try again.'));
    }

    /**
     * @param  Customer  $customer
     * @return bool
     *
     * @throws Exception
     */
    public function destroy(Customer $customer): bool
    {
        if ($customer->forceDelete()) {
            event(new CustomerDestroyed($customer));

            return true;
        }

        throw new Exception(__('There was a problem permanently deleting this customer. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return Customer
     */
    protected function createCustomer(array $data = []): Customer
    {
        return $this->model::create([
            'slug' => Str::slug($data['name'].'-'.$data['email'].'-'.$data['phone'].'-'.$data['cmnd']),
            'name' => $data['name'],
            'email' => $data['email'],
            'cmnd' => $data['cmnd'],
            'phone' => $data['phone'],
            'address' => $data['address'],
            'birth_day' => $data['birth_day'],
            'note' => $data['note'],
            'province_id' => $data['province_id'],
            'district_id' => $data['district_id'],
            'ward_id' => $data['ward_id'],
            'user_id' => $data['user_id'],
            'active' => isset($data['active']) && $data['active'] == '1',
            'branch_id' => Auth::user()->branch_id,
            'type' => $data['type'],
        ]);
    }
}
