<?php

namespace App\Services;

use App\Events\PosBack\PosBackCreated;
use App\Events\PosBack\PosBackDeleted;
use App\Events\PosBack\PosBackDestroyed;
use App\Events\PosBack\PosBackRestored;
use App\Events\PosBack\PosBackStatusChanged;
use App\Events\PosBack\PosBackUpdated;
use App\Events\PosBack\PosBackVerify;
use App\Models\PosBack\PosBack;
use App\Exceptions\GeneralException;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\Pos\Pos;
use App\Models\PosBackDetail\PosBackDetail;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Google\Service\Monitoring\Custom;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class PosBackService.
 */
class PosBackService extends BaseService
{
    /*
     * PosBackService constructor.
     *
     * @param  PosBack  $posBack
     */
    public function __construct(PosBack $posBack)
    {
        $this->model = $posBack;
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
     * @return PosBack
     *
     * @throws GeneralException
     * @throws \Throwable
     */
    public function store(array $data = []): PosBack
    {
        DB::beginTransaction();
        $money =  (float)(str_replace(',', '', $data['money']));
        try {
            $posBack = $this->createPosBack([
                    'name' => $data['name'],
                    'money' => $money,
                    'pos_id' => $data['pos_id'] ?? null,
                    'note' => $data['note'],
                    'lo' => $data['lo'] ?? 100,
                    'active' => isset($data['active']) && $data['active'] === '1',
                    'user_id' => Auth::user()->id,
                    
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem creating this posBack. Please try again.'));
        }
        event(new PosBackCreated($posBack));
        
        DB::commit();
        return $posBack;
    }

    /**
     * @param  PosBack  $posBack
     * @param  array  $data
     * @return PosBack
     *
     * @throws \Throwable
     */
    public function update(PosBack $posBack, array $data = []): PosBack
    {
        DB::beginTransaction();
        $money =  (float)(str_replace(',', '', $data['money']));
        try {
            $posBack->update([
                'name' => $data['name'],
                'money' => $money,
                'pos_id' => $data['pos_id'] ?? null,
                'note' => $data['note'],
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this posBack. Please try again.'));
        }
        event(new PosBackUpdated($posBack));

        DB::commit();
        return $posBack;
    }


    /**
     * @param  PosBack  $posBack
     * @param $status
     * @return PosBack
     *
     * @throws GeneralException
     */
    public function mark(PosBack $posBack, $status): PosBack
    {
        $posBack->active = $status;

        if ($posBack->save()) {
            event(new PosBackStatusChanged($posBack, $status));

            return $posBack;
        }

        throw new GeneralException(__('There was a problem updating this posBack. Please try again.'));
    }

    /**
     * @param  PosBack  $posBack
     * @return PosBack
     *
     * @throws GeneralException
     */
    public function delete(PosBack $posBack): PosBack
    {

        if ($this->deleteById($posBack->id)) {
            event(new PosBackDeleted($posBack));

            return $posBack;
        }

        throw new GeneralException('There was a problem deleting this posBack. Please try again.');
    }

    /**
     * @param  PosBack  $posBack
     * @return PosBack
     *
     * @throws GeneralException
     */
    public function restore(PosBack $posBack): PosBack
    {
        if ($posBack->restore()) {
            event(new PosBackRestored($posBack));

            return $posBack;
        }

        throw new GeneralException(__('There was a problem restoring this posBack. Please try again.'));
    }

    /**
     * @param  PosBack  $posBack
     * @return bool
     *
     * @throws GeneralException
     */
    public function destroy(PosBack $posBack): bool
    {
        if ($posBack->forceDelete()) {
            event(new PosBackDestroyed($posBack));

            return true;
        }

        throw new GeneralException(__('There was a problem permanently deleting this posBack. Please try again.'));
    }

    /**
     * @param  array  $data
     * @return PosBack
     */
    protected function createPosBack(array $data = []): PosBack
    {

        return $this->model::create([
            'name' => $data['name'],
            'money' => $data['money'],
            'pos_id' => $data['pos_id'] ?? null,
            'note' => $data['note'],
            'lo' => $data['lo'],
            'active' => $data['active'],
        ]);
    }
}
