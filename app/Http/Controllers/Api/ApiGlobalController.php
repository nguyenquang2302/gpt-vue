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

/**
 * Class DashboardController.
 */
class ApiGlobalController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */

    public function listPos()
    {
        $pos = Pos::all();

        return response()->json($pos, Response::HTTP_OK);
    }
    public function provinces()
    {
        $provinces = Province::all();

        return response()->json($provinces, Response::HTTP_OK);
    }

    public function districts($provinceId)
    {
        $province = Province::find($provinceId);
        $districts =[];
        if($province) {
            $districts = $province->districts;
        }

        return response()->json($districts, Response::HTTP_OK);
    }

    public function wards($districtId)
    {
        $district = District::find($districtId);
        $wards =[];
        if($district) {
            $wards = $district->wards;
        }

        return response()->json($wards, Response::HTTP_OK);
    }
    
    public function address()
    {
        $address = [];
        $provinces = Province::all();
        foreach($provinces as $province) {
            $districts = $province->districts;
            foreach($districts as $district) {
                $wards = $district->wards;
                foreach($wards as $ward) {
                    $address[] = ['id' => $ward->id,'name' => $province->name .' > '.$district->name.' > '. $ward->name ];
                }
            }
        }
        return response()->json($address, Response::HTTP_OK);
    }

    public function listBank() {
        $banks = Bank::all();
        
        return response()->json($banks, Response::HTTP_OK);
    }

    public function fundCategories() {
        $fundCategories = FundCategory::all();
        return response()->json($fundCategories, Response::HTTP_OK);

    }

}