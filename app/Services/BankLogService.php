<?php

namespace App\Services;

use App\Events\BankLog\BankLogUpdated;
use App\Models\BankLog\BankLog;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Auth;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Class BankLogService.
 */
class BankLogService extends BaseService
{
    /*
     * BankLogService constructor.
     *
     * @param  BankLog  $bankLog
     */
    public function __construct(BankLog $bankLog)
    {
        $this->model = $bankLog;
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

    public function update(BankLog $bankLog, array $data = []): BankLog
    {
        DB::beginTransaction();
        $dataUpdate = [
            'content_fix' => $data['content_fix'],
            'note' => $data['note'],
            'name' => $data['name'],
            'fund_category_id' => $data['fund_category_id'],
        ];
        if( Auth::user()->checkRole(['admin'])) {
            $dataUpdate['benAccountNo'] = $data['benAccountNo'] ?? '';
        }
        try {
            $bankLog->update($dataUpdate);
        } catch (Exception $e) {
            DB::rollBack();

            throw new GeneralException(__('There was a problem updating this bankLog. Please try again.'));
        }

        event(new BankLogUpdated($bankLog));

        DB::commit();

        return $bankLog;
    }


}
