<?php

namespace App\Http\Controllers\Api;

use App\Models\DrawalDetail\DrawalDetail;
use App\Models\PosConsignment\PosConsignment;
use App\Models\WithdrawalDetail\WithdrawalDetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

/**
 * Class DashboardController.
 */
class BillReturnController
{
    public function billReturn(Request $request)
    {

        if ($request->get('type') === 'withdrawal') {
            $detail =  WithdrawalDetail::find($request->get('id'));

            if (!$detail) {
                return response([
                    'message' => 'Giao dịch không tồn tại'
                ], Response::HTTP_FOUND);
            }
            if ($detail->bill_return == false) {
                $posConsignment =  PosConsignment::where('pos_id', $detail->pos_id)->where('lo', $detail->lo)->first();
                if ($posConsignment->isDone == true) {
                    $posConsignment->isDone == false;
                    $posConsignment->save();
                }
                $detail->bill_return = true;
                $detail->save();
                return response([
                    'message' => 'Đã xác nhận thành công giao dịch'
                ], Response::HTTP_OK);
            } else {
                return response([
                    'message' => 'Giao dịch đã được xác nhận trước đó'
                ], Response::HTTP_FOUND);
            }
        }
        if ($request->get('type') === 'drawal') {
            $detail =  DrawalDetail::find($request->get('id'));
            if (!$detail) {
                return response([
                    'message' => 'Giao dịch không tồn tại'
                ], Response::HTTP_FOUND);
            }
            $posConsignment =  PosConsignment::where('pos_id', $detail->pos_id)->where('lo', $detail->lo)->first();
            if ($posConsignment->isDone == true) {
                $posConsignment->isDone == false;
                $posConsignment->save();
            }
            if ($detail->bill_return == false) {
                $detail->bill_return = true;
                $detail->save();
                return response([
                    'message' => 'Đã xác nhận thành công giao dịch'
                ], Response::HTTP_OK);
            } else {
                return response([
                    'message' => 'Giao dịch đã được xác nhận trước đó'
                ], Response::HTTP_FOUND);
            }
        }
    }
}
