<?php

namespace App\Services;

use App\Events\CustomerCard\CustomerCardCreated;
use App\Events\CustomerCard\CustomerCardDeleted;
use App\Events\CustomerCard\CustomerCardDestroyed;
use App\Events\CustomerCard\CustomerCardRestored;
use App\Events\CustomerCard\CustomerCardStatusChanged;
use App\Events\CustomerCard\CustomerCardUpdated;
use App\Models\CustomerCard\CustomerCard;
use App\Exceptions\GeneralException;
use App\Models\Customer\Customer;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Google\Service\Monitoring\Custom;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class CustomerCardService.
 */
class CustomerCardService extends BaseService
{
    /*
     * CustomerCardService constructor.
     *
     * @param  CustomerCard  $customerCard
     */
    public function __construct(CustomerCard $customerCard)
    {
        $this->model = $customerCard;
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
     * @return CustomerCard
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): CustomerCard
    {
        DB::beginTransaction();
        $currency_limit =  (float)(str_replace(',', '', $data['currency_limit']));
        try {
            $customerCard = $this->createCustomerCard([
                'name' => $data['name'],
                'start_number' => $data['start_number'],
                'end_number' => $data['end_number'],
                'day_statement' => $data['day_statement'],
                'currency_limit' => $currency_limit,
                'customer_id' => $data['customer_id'],
                'bank_id' => $data['bank_id'],
                'note' => $data['note'],
                'due_date' => $data['due_date'],
                'card_number' => $data['card_number'],
                'active' => isset($data['active']) && $data['active'] === '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this customerCard. Please try again.'));
        }
        event(new CustomerCardCreated($customerCard));

        DB::commit();

        return $customerCard;
    }

    /**
     * @param  CustomerCard  $customerCard
     * @param  array  $data
     * @return CustomerCard
     *
     * @throws \Throwable
     */
    public function update(CustomerCard $customerCard, array $data = []): CustomerCard
    {
        DB::beginTransaction();
        $currency_limit =  (float)(str_replace(',', '', $data['currency_limit']));
        try {
            $customerCard->update([
                'name' => $data['name'],
                'start_number' => $data['start_number'],
                'end_number' => $data['end_number'],
                'day_statement' => $data['day_statement'],
                'currency_limit' => $currency_limit,
                'bank_id' => $data['bank_id'],
                'note' => $data['note'],
                'due_date' => $data['due_date'],
                'card_number' => $data['card_number'],
                'active' => isset($data['active']) && $data['active'] === '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this customerCard. Please try again.'));
        }

        event(new CustomerCardUpdated($customerCard));

        DB::commit();

        return $customerCard;
    }


    /**
     * @param  CustomerCard  $customerCard
     * @param $status
     * @return CustomerCard
     *
     * @throws GeneralException
     */
    public function mark(CustomerCard $customerCard, $status): CustomerCard
    {
        $customerCard->active = $status;

        if ($customerCard->save()) {
            event(new CustomerCardStatusChanged($customerCard, $status));

            return $customerCard;
        }

        throw new GeneralException(__('There was a problem updating this customerCard. Please try again.'));
    }

    /**
     * @param  CustomerCard  $customerCard
     * @return CustomerCard
     *
     * @throws GeneralException
     */
    public function delete(CustomerCard $customerCard): CustomerCard
    {

        if ($this->deleteById($customerCard->id)) {
            event(new CustomerCardDeleted($customerCard));

            return $customerCard;
        }

        throw new GeneralException('There was a problem deleting this customerCard. Please try again.');
    }

    /**
     * @param  CustomerCard  $customerCard
     * @return CustomerCard
     *
     * @throws GeneralException
     */
    public function restore(CustomerCard $customerCard): CustomerCard
    {
        if ($customerCard->restore()) {
            event(new CustomerCardRestored($customerCard));

            return $customerCard;
        }

        throw new GeneralException(__('There was a problem restoring this customerCard. Please try again.'));
    }

    /**
     * @param  CustomerCard  $customerCard
     * @return bool
     *
     * @throws GeneralException
     */
    public function destroy(CustomerCard $customerCard): bool
    {
        if ($customerCard->forceDelete()) {
            event(new CustomerCardDestroyed($customerCard));

            return true;
        }

        throw new GeneralException(__('There was a problem permanently deleting this customerCard. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return CustomerCard
     */
    protected function createCustomerCard(array $data = []): CustomerCard
    {
        return $this->model::create([
            'name' => $data['name'],
            'start_number' => $data['start_number'],
            'end_number' => $data['end_number'],
            'day_statement' => $data['day_statement'],
            'currency_limit' => $data['currency_limit'],
            'customer_id' => $data['customer_id'],
            'bank_id' => $data['bank_id'],
            'note' => $data['note'],
            'due_date' => $data['due_date'],
            'card_number' => $data['card_number'],
            'branch_id' => Customer::find($data['customer_id'])->branch_id,
            'active' => isset($data['active']) && $data['active'] == '1',
        ]);
    }

    public function updateStatement(CustomerCard $customerCard, array $data = []): CustomerCard
    {
        DB::beginTransaction();
        $currency_payment = (float)(str_replace(',', '', $data['currency_payment']));
        $due_date2 = $data['due_date2'] ? Carbon::createFromFormat('d/m/Y', $data['due_date2'])->format('Y-m-d') : NULL;
        try {
            $customerCard->update([
                'currency_payment' => $currency_payment ?? 0,
                'due_date2' => $due_date2,
                'date_comlate' => NULL,
            ]);

        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this customerCard. Please try again.'));
        }

        event(new CustomerCardUpdated($customerCard));

        DB::commit();

        return $customerCard;
    }

}
