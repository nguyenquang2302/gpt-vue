<?php

namespace App\Services;

use App\Events\Pos\PosCreated;
use App\Events\Pos\PosDeleted;
use App\Events\Pos\PosDestroyed;
use App\Events\Pos\PosRestored;
use App\Events\Pos\PosStatusChanged;
use App\Events\Pos\PosUpdated;
use App\Models\Pos\Pos;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class PosService.
 */
class PosService extends BaseService
{
    /*
     * PosService constructor.
     *
     * @param  Pos  $pos
     */
    public function __construct(Pos $pos)
    {
        $this->model = $pos;
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
     * @return Pos
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): Pos
    {
        DB::beginTransaction();
        $currency_limit =  (float)(str_replace(',', '', $data['currency_limit']));
        $currency_limit_on_card =  (float)(str_replace(',', '', $data['currency_limit_on_card']));
        $bill_limit_on_card =  (float)(str_replace(',', '', $data['bill_limit_on_card']));
        $fee_bank =  (float)(str_replace(',', '', $data['fee_bank']));
        $currency_limit_on_bill =  (float)(str_replace(',', '', $data['currency_limit_on_bill']));
        
        try {
            $pos = $this->createPos([
                    'name' => $data['name'],
                    'currency_limit' => $currency_limit,
                    'currency_limit_on_card' => $currency_limit_on_card,
                    'bill_limit_on_card' => $bill_limit_on_card,
                    'currency_limit_on_bill' => $currency_limit_on_bill,
                    'fee_bank' => $fee_bank,
                    'user_id_belongto' => $data['user_id_belongto'],
                    'bank_id' => $data['bank_id'],
                    'note' => $data['note'],
                    'telegramChanelId' => $data['telegramChanelId'],
                    'active' => isset($data['active']) && $data['active'] === '1',
                    'user_id' => Auth::user()->id
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this pos. Please try again.'));
        }
        event(new PosCreated($pos));
        DB::commit();

        return $pos;
    }

    /**
     * @param  Pos  $pos
     * @param  array  $data
     * @return Pos
     *
     * @throws \Throwable
     */
    public function update(Pos $pos, array $data = []): Pos
    {
        DB::beginTransaction();
        $currency_limit =  (float)(str_replace(',', '', $data['currency_limit']));
        $currency_limit_on_card =  (float)(str_replace(',', '', $data['currency_limit_on_card']));
        $bill_limit_on_card =  (float)(str_replace(',', '', $data['bill_limit_on_card']));
        $fee_bank =  (float)(str_replace(',', '', $data['fee_bank']));
        $currency_limit_on_bill =  (float)(str_replace(',', '', $data['currency_limit_on_bill']));
        try {
            $pos->update([
                'name' => $data['name'],
                'currency_limit' => $currency_limit,
                'currency_limit_on_card' => $currency_limit_on_card,
                'bill_limit_on_card' => $bill_limit_on_card,
                'currency_limit_on_bill' => $currency_limit_on_bill,
                'fee_bank' => $fee_bank,
                'user_id_belongto' => $data['user_id_belongto'],
                'bank_id' => $data['bank_id'],
                'note' => $data['note'],
                'telegramChanelId' => $data['telegramChanelId'],
                'active' => isset($data['active']) && $data['active'] === '1',
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this pos. Please try again.'));
        }

        event(new PosUpdated($pos));

        DB::commit();

        return $pos;
    }


    /**
     * @param  Pos  $pos
     * @param $status
     * @return Pos
     *
     * @throws GeneralException
     */
    public function mark(Pos $pos, $status): Pos
    {
        $pos->active = $status;

        if ($pos->save()) {
            event(new PosStatusChanged($pos, $status));

            return $pos;
        }

        throw new GeneralException(__('There was a problem updating this pos. Please try again.'));
    }

    /**
     * @param  Pos  $pos
     * @return Pos
     *
     * @throws GeneralException
     */
    public function delete(Pos $pos): Pos
    {

        if ($this->deleteById($pos->id)) {
            event(new PosDeleted($pos));

            return $pos;
        }

        throw new GeneralException('There was a problem deleting this pos. Please try again.');
    }

    /**
     * @param  Pos  $pos
     * @return Pos
     *
     * @throws GeneralException
     */
    public function restore(Pos $pos): Pos
    {
        if ($pos->restore()) {
            event(new PosRestored($pos));

            return $pos;
        }

        throw new GeneralException(__('There was a problem restoring this pos. Please try again.'));
    }

    /**
     * @param  Pos  $pos
     * @return bool
     *
     * @throws GeneralException
     */
    public function destroy(Pos $pos): bool
    {
        if ($pos->forceDelete()) {
            event(new PosDestroyed($pos));

            return true;
        }

        throw new GeneralException(__('There was a problem permanently deleting this pos. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return Pos
     */
    protected function createPos(array $data = []): Pos
    {
        return $this->model::create([
            'name' => $data['name'],
            'currency_limit' => $data['currency_limit'],
            'currency_limit_on_card' => $data['currency_limit_on_card'],
            'bill_limit_on_card' => $data['bill_limit_on_card'],
            'currency_limit_on_bill' => $data['currency_limit_on_bill'],
            'fee_bank' => $data['fee_bank'],
            'user_id_belongto' => $data['user_id_belongto'],
            'user_id' => $data['user_id'],
            'bank_id' => $data['bank_id'],
            'note' => $data['note'],
            'telegramChanelId' => $data['telegramChanelId'],
            'active' => isset($data['active']) && $data['active'] == '1',
        ]);
    }
}
