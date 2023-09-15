<?php

namespace App\Services;

use App\Events\Bank\BankCreated;
use App\Events\Bank\BankDeleted;
use App\Events\Bank\BankDestroyed;
use App\Events\Bank\BankRestored;
use App\Events\Bank\BankStatusChanged;
use App\Events\Bank\BankUpdated;
use App\Models\Bank\Bank;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class BankService.
 */
class BankService extends BaseService
{
    /*
     * BankService constructor.
     *
     * @param  Bank  $bank
     */
    public function __construct(Bank $bank)
    {
        $this->model = $bank;
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
     * @return Bank
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): Bank
    {
        DB::beginTransaction();

        // try {
            $bank = $this->createBank([
                'name' => $data['name'],
                'bin' => $data['accId'],
                'shortName' => $data['shortName'],
                'active' => isset($data['active']) && $data['active'] === '1',
            ]);
        // } catch (Exception $e) {
        //     DB::rollBack();

        //     throw new GeneralException(__('There was a problem creating this bank. Please try again.'));
        // }

        event(new BankCreated($bank));

        DB::commit();

        return $bank;
    }

    /**
     * @param  Bank  $bank
     * @param  array  $data
     * @return Bank
     *
     * @throws \Throwable
     */
    public function update(Bank $bank, array $data = []): Bank
    {
        DB::beginTransaction();

        try {
            $bank->update([
                'name' => $data['name'],
                'bin' => $data['accId'],
                'shortName' => $data['shortName'],
                'active' => isset($data['active']) && $data['active'] === '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this bank. Please try again.'));
        }

        event(new BankUpdated($bank));

        DB::commit();

        return $bank;
    }


    /**
     * @param  Bank  $bank
     * @param $status
     * @return Bank
     *
     * @throws GeneralException
     */
    public function mark(Bank $bank, $status): Bank
    {
        $bank->active = $status;

        if ($bank->save()) {
            event(new BankStatusChanged($bank, $status));

            return $bank;
        }

        throw new GeneralException(__('There was a problem updating this bank. Please try again.'));
    }

    /**
     * @param  Bank  $bank
     * @return Bank
     *
     * @throws GeneralException
     */
    public function delete(Bank $bank): Bank
    {
        if ($bank->id === auth()->id()) {
            throw new GeneralException(__('You can not delete yourself.'));
        }

        if ($this->deleteById($bank->id)) {
            event(new BankDeleted($bank));

            return $bank;
        }

        throw new GeneralException('There was a problem deleting this bank. Please try again.');
    }

    /**
     * @param  Bank  $bank
     * @return Bank
     *
     * @throws GeneralException
     */
    public function restore(Bank $bank): Bank
    {
        if ($bank->restore()) {
            event(new BankRestored($bank));

            return $bank;
        }

        throw new GeneralException(__('There was a problem restoring this bank. Please try again.'));
    }

    /**
     * @param  Bank  $bank
     * @return bool
     *
     * @throws GeneralException
     */
    public function destroy(Bank $bank): bool
    {
        if ($bank->forceDelete()) {
            event(new BankDestroyed($bank));

            return true;
        }

        throw new GeneralException(__('There was a problem permanently deleting this bank. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return Bank
     */
    protected function createBank(array $data = []): Bank
    {
        return $this->model::create([
            'name' => $data['name'],
            'bin' => $data['bin'],
            'shortName' => $data['shortName'],
            'active' => isset($data['active']) && $data['active'] =='1',
        ]);
    }
}
