<?php

namespace App\Http\Controllers\Api;

use App\Models\Bank\Bank;
use App\Models\Branch\Branch;
use App\Models\FundCategory\FundCategory;
use App\Models\Pos\Pos;
use Carbon\Carbon;
use Exception;
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
        $time_check_mb = settings()->get('time_check_mb', 0);
        if ($time_check_mb) {
            $time_check_mb_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $time_check_mb);
        } else {
            $time_check_mb_carbon = Carbon::now()->subHour(5);
        }
        $now = Carbon::now();
        $caculator_minutes = $time_check_mb_carbon->diffInMinutes($now);

        if ($caculator_minutes <= 5) {
            return response([
                'message' => 'Vui lòng chờ '.(5-$caculator_minutes).' Phút để thực hiện tiếp',
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }
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

        $time_check_bank_log = settings()->get('time_check_bank_log', 0);
        if ($time_check_bank_log) {
            $time_check_bank_log_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $time_check_bank_log);
        } else {
            $time_check_bank_log_carbon = Carbon::now()->subHour(5);
        }
        $now = Carbon::now();
        $caculator_minutes = $time_check_bank_log_carbon->diffInMinutes($now);
        if ($caculator_minutes <= 5) {
            return response([
                'message' => 'Vui lòng chờ '.(5-$caculator_minutes).' Phút để thực hiện tiếp',
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }

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

        $time_check_pos_back = settings()->get('time_check_pos_back', 0);
        if ($time_check_pos_back) {
            $time_check_pos_back_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $time_check_pos_back);
        } else {
            $time_check_pos_back_carbon = Carbon::now()->subHour(5) ;
        }
        $now = Carbon::now();
        $caculator_minutes = $time_check_pos_back_carbon->diffInMinutes($now);
        if ($caculator_minutes <= 5) {
            return response([
                'message' => 'Vui lòng chờ '.(5-$caculator_minutes).' Phút để thực hiện tiếp',
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }

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

    public function checkAll() {

        $time_check_pos_back = settings()->get('time_check_pos_back', 0);
        $time_check_bank_log = settings()->get('time_check_bank_log', 0);
        $time_check_mb = settings()->get('time_check_mb', 0);
      
        if ($time_check_pos_back) {
            $time_check_pos_back_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $time_check_pos_back);
            $time_check_bank_log_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $time_check_bank_log);
            $time_check_mb_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $time_check_mb);
        } else {
            $time_check_pos_back_carbon = Carbon::now()->subHour(5) ;
        }
        $now = Carbon::now();
        $caculator_pos_minutes = $time_check_pos_back_carbon->diffInMinutes($now);
        $caculator_mb_minutes = $time_check_bank_log_carbon->diffInMinutes($now);
        $caculator_banklog_minutes = $time_check_mb_carbon->diffInMinutes($now);

        if ($caculator_pos_minutes <= 5 || $caculator_mb_minutes <= 5 || $caculator_banklog_minutes <=5 ) {
            $max = ($caculator_pos_minutes < $caculator_mb_minutes)?$caculator_pos_minutes:$caculator_mb_minutes;
            $max = ($max < $caculator_banklog_minutes)?$max:$caculator_banklog_minutes;
            
            return response([
                'message' => 'Vui lòng chờ '.(5-$max).' Phút để thực hiện tiếp',
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }

        try {
            Artisan::call('bank:MBCheck');
            Artisan::call('banklogs:check');
            Artisan::call('posback:check');
            return response([
                'message' => 'Kiêm  tiền  thành công',
            ], Response::HTTP_OK);
            
        } catch (Exception $exception) {
            return response([
                'message' => 'Có lỗi xảy ra',
                'th' => $exception->getMessage()
            ], Response::HTTP_GATEWAY_TIMEOUT);
        }

    }

}