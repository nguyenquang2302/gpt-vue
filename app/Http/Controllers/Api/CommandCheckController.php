<?php

namespace App\Http\Controllers\Api;

use App\Models\Bank\Bank;
use App\Models\Branch\Branch;
use App\Models\FundCategory\FundCategory;
use App\Models\Pos\Pos;
use HoangPhi\VietnamMap\Models\Province;
use HoangPhi\VietnamMap\Models\District;
use HoangPhi\VietnamMap\Models\Ward;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;

/**
 * Class DashboardController.
 */
class CommandCheckController
{
    public function checkHistories() {
        try {
            Artisan::call('bank:MBCheck');

            return response([
                'message' => 'Lấy lịch sử thành công',
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            return response([
                'message' => 'Có lỗi xảy ra',
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }

    }
    public function checkBankLogs() {
        try {
            Artisan::call('banklogs:check');

            return response([
                'message' => 'Kiểm tra lịch sử thành công',
            ], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            return response([
                'message' => 'Có lỗi xảy ra',
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }

        
    }

    public function checkPosbacks() {
        try {
            Artisan::call('posback:check');
            return response([
                'message' => 'Kiêm tra tiền về thành công',
            ], Response::HTTP_OK);
            
        } catch (\Throwable $th) {
            return response([
                'message' => 'Có lỗi xảy ra',
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }

    }

}