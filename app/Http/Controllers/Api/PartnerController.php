<?php

namespace App\Http\Controllers\Api;

use App\Models\PartnerTransaction\PartnerTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DashboardController.
 */
class PartnerController
{
    public function partner(Request $request)
    {   
        $active = 1;
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::parse($request->get('0'));
            $data['to'] =  $to = Carbon::parse($request->get('1'));
        } else if($case = $request->get('3')) {
            switch ($case) {
                case 'toDay':
                    $data['from'] =  $from = Carbon::now();
                    $data['to'] =  $to = Carbon::now();
                    break;
                case 'tomorrow':
                        $data['from'] =  $from = Carbon::now()->subDay();
                        $data['to'] =  $to = Carbon::now()->subDay();
                        break;
                case 'sub7Day':
                        $data['from'] =  $from = Carbon::now()->subDays(7);
                        $data['to'] =  $to = Carbon::now();
                        break;
                case 'sub14Day':
                        $data['from'] =  $from = Carbon::now()->subDays(14);
                        $data['to'] =  $to = Carbon::now();
                        break;
                case 'sub30Day':
                        $data['from'] =  $from = Carbon::now()->subDays(30);
                        $data['to'] =  $to = Carbon::now();
                        break;
                case 'thisWeek':
                        $data['from'] =  $from = Carbon::now()->startOfWeek();
                        $data['to'] =  $to = Carbon::now()->endOfWeek();
                        break;
                case 'lastWeek':
                        $data['from'] =  $from = Carbon::now()->subWeek()->startOfWeek();
                        $data['to'] =  $to = Carbon::now()->subWeek()->endOfWeek();
                        break;
                case 'thisMonth':
                        $data['from'] =  $from = Carbon::now()->startOfMonth();
                        $data['to'] =  $to = Carbon::now()->endOfMonth();
                        break;
                case 'lastMonth':
                        $data['from'] =  $from = Carbon::now()->subMonth()->startOfMonth();
                        $data['to'] =  $to = Carbon::now()->subMonth()->endOfMonth();
                        break;
                default:
                    $data['from'] =  $from = Carbon::now();
                    $data['to'] =  $to = Carbon::now();
                    break;
            }
        }
        else {
            $data['from'] =  $from = Carbon::now();
            $data['to'] =  $to = Carbon::now();
        }
       
        $user  = Auth::user();
        $data['lists'] = PartnerTransaction::whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])->where('user_id',$user->id)->get();
       
        // dd($array);
        return response([
            'data' =>  $data,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);

    }

}
