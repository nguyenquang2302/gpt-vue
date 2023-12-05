<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Backend\Dashboard\DashboardRequest;
use App\Models\BankLog\BankLog;
use App\Models\Branch\Branch;
use App\Models\Customer\Customer;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Drawal\Drawal;
use App\Models\DrawalDetail\DrawalDetail;
use App\Models\Expense\Expense;
use App\Models\GlobalDetail\GlobalDetail;
use App\Models\Pos\Pos;
use App\Models\Users\User;
use App\Models\Withdrawal\Withdrawal;
use App\Models\WithdrawalDetail\WithdrawalDetail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class DashboardController.
 */
class DashboardController
{
    public function transaction(Request $request)
    {

        $active = 1;
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::parse($request->get('from'));
            $data['to'] =  $to = Carbon::parse($request->get('to'));
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
        $detail1 =  DrawalDetail::whereHas('drawal', function (Builder $q1) use ($active, $from, $to) {
            $q1->where('isDone', $active)
                ->whereBetween('datetime', [$from->startOfDay(), $to->endOfDay()]);
        })->get();
        $detail2 =  WithdrawalDetail::whereHas('withdrawal', function (Builder $q1) use ($active, $from, $to) {
            $q1->where('isDone', $active)
                ->whereBetween('datetime', [$from->startOfDay(), $to->endOfDay()]);
        })->get();
        $array = [];
        $data['money'] = 0;
        $data['sl'] = 0;
        $data['total_pos_back'] =0;
        $data['total_money'] =0;
        $data['total_fee_ship'] =0;
        $data['total_fee_customer'] =0;
        $data['total_profit'] =0;
        $data['total_fee_bank_money'] =0;
        $k = 0;
        foreach ($detail1 as $d) {
            $k++;
            if(!isset($data['posLists'][$d->pos_id])) {
                $data['posLists'][$d->pos_id]['profit'] = 0;
                $data['posLists'][$d->pos_id]['name'] = $d->pos->name;
            }
            $data['posLists'][$d->pos_id]['profit']+= $d->profit;
            $data['sl'] ++;
            $data['money'] += $d->profit;
            $d->drawal->stt = $d->drawal->stt??0;
            $key =  rand(11,88).'_'.$k.'_'.str_replace('_','',$d->pos_id.$d->lo.$d->bill);
            $array[$key]['pos_name'] = $d->pos->name;
            $array[$key]['stt'] = $d->drawal->stt;
            $array[$key]['id'] = $d->id;
            $array[$key]['bill_return'] = $d->bill_return;
            $array[$key]['type'] = 'drawal';
            $array[$key]['lo'] = $d->lo;
            $array[$key]['bill'] = $d->bill;
            $array[$key]['money'] = $d->money;
            $array[$key]['customerCard'] = $d->customerCard ? $d->customerCard->name : '';
            $array[$key]['detail'] = $d;
            $array[$key]['money_back'] = $d->money_back;
            $array[$key]['fee_ship'] = $d->fee_ship;
            $array[$key]['fee_customer'] = $d->drawal->fee_customer;
            $array[$key]['profit'] = $d->profit;
            $array[$key]['fee_bank'] = $d->fee_bank;
            $array[$key]['fee_bank_money'] = $d->fee_bank_money;
            $array[$key]['fee_customer_money'] = $d->drawal->fee_customer*$d->money/100;
            $data['total_pos_back'] +=$d->money_back;
            $data['total_money'] +=$d->money;
            $data['total_fee_ship'] +=$d->fee_ship;
            $data['total_fee_customer'] +=$d->fee_customer;
            $data['total_profit'] +=$d->profit;
            $data['total_fee_bank_money'] +=$d->fee_bank_money;
            

        }
        foreach ($detail2 as $d) {
            $k++;
            if(!isset($data['posLists'][$d->pos_id])) {
                $data['posLists'][$d->pos_id]['profit'] = 0;
                $data['posLists'][$d->pos_id]['name'] = $d->pos->name;
            }
            $data['posLists'][$d->pos_id]['profit']+= $d->profit;
            $data['sl'] ++;
            $data['money'] += $d->profit;
            $d->withdrawal->stt = $d->withdrawal->stt??0;
            $key =  rand(11,88).'_'.$k.'_'.str_replace('_','',$d->pos_id.$d->lo.$d->bill);
            
            $array[$key]['id'] = $d->id;
            $array[$key]['bill_return'] = $d->bill_return;
            $array[$key]['type'] = 'withdrawal';
            $array[$key]['pos_name'] = $d->pos->name;
            $array[$key]['stt'] = $d->withdrawal->stt;
            $array[$key]['lo'] = $d->withdrawal->stt.$d->lo;
            $array[$key]['bill'] = $d->bill;
            $array[$key]['money'] = $d->money_drawal;
            $array[$key]['customerCard'] = $d->withdrawal->customerCard ? $d->withdrawal->customerCard->name : '';
            $array[$key]['detail'] = $d;
            $array[$key]['money_back'] = $d->money_back;
            $array[$key]['fee_ship'] = $d->fee_ship;
            $array[$key]['fee_customer'] = $d->withdrawal->fee_customer;
            $array[$key]['profit'] = $d->profit;
            $array[$key]['fee_bank'] = $d->fee_bank;
            $array[$key]['fee_bank_money'] =  $d->fee_bank*$d->money_drawal/100;
            $array[$key]['fee_customer_money'] = $d->withdrawal->fee_customer*$d->money_drawal/100;
            $data['total_pos_back'] +=$d->money_back;
            $data['total_money'] +=$d->money;
            $data['total_fee_ship'] +=$d->fee_ship;
            $data['total_fee_customer'] +=$d->fee_customer;
            $data['total_profit'] +=$d->profit;
            $data['total_fee_bank_money'] +=$d->fee_bank_money;


        }
        ksort($array);
        $data['lists'] = $array;
        // dd($array);
        return response([
            'data' =>  $data,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);

    }


    public function expense(Request $request)
    {

        $active = 1;
        $data['from'] =  $from = Carbon::now();
        $data['to'] =  $to = Carbon::now();
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
                case 'all':
                    $type = $request->get('type');
                    if($type === 'invest') {
                        $data['expences']  = Expense::with('fundCategory')->whereHas('fundCategory', function (Builder $q1) {
                            $q1->where('type',1);
                        })->get();
                    } else if($type === 'operate') {
                        $data['expences']  = Expense::with('fundCategory')->whereHas('fundCategory', function (Builder $q1) {
                            $q1->where('type',0)->orWhere('type',null);
                        })->get();
                    } else {
                        $data['expences']  = Expense::with('fundCategory')->get();
                    }
                    break;
                default:
                    $data['from'] =  $from = Carbon::now();
                    $data['to'] =  $to = Carbon::now();
                    break;
            }
        }
        
        if(!isset($data['expences'])) {

            $type = $request->get('type');
            if($type === 'invest') {
                $data['expences']  = Expense::with('fundCategory')->with('bankLog')->whereHas('fundCategory', function (Builder $q1) {
                    $q1->where('type',1);
                })->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])->get();
            } else if($type === 'operate') {
                $data['expences']  = Expense::with('fundCategory')->with('bankLog')->whereHas('fundCategory', function (Builder $q1) {
                    $q1->where('type',0)->orWhere('type',null);
                })->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])->get();

            } else {
                $data['expences']  = Expense::with('fundCategory')->with('bankLog')->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])->get();
            }
        }
        foreach($data['expences']  as $expence) {
            if($expence->note === 'N/A') {
                if($banklog = $expence->bankLog) {
                    $expence->note = $banklog->benAccountNo;
                    $expence->save();
                }
            }
        }
        $data['totalCreditAmount'] =  $data['expences']->sum('creditAmount');
        $data['totalDebitAmount'] =  $data['expences']->sum('debitAmount');
        return response([
            'data' =>  $data,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);

    }

    public function indexGlobal(Request $request) {
       
        $data1['users'] = User::where('activeBank',true)->get()->toArray();
        $data1['settings'] = settings()->all()->toArray();
        return response([
            'data' =>  $data1,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);
    }

    public function dashboardPos(DashboardRequest $request)
    {

        $data['now'] = Carbon::now();
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::parse($request->get('from'));
            $data['to'] =  $to = Carbon::parse($request->get('to'));
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

        // $data['all_pos'] = Pos::with(['posConsignment' => function ($query) use ($from, $to) { 
            $data['all_pos'] = Pos::where('active',true)->with(['posConsignment' => function ($query) use ($from, $to) { 
            $query->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
        }])->get();
        $data['total'] = 0;
        $data['pos_back_money'] = 0;
        foreach($data['all_pos'] as $pos) {
            $data['pos_back_money']+= $pos->pos_back_money = $pos->posConsignment->sum('money');
            $data['total'] += $pos->total = $pos->posConsignment->sum('total_pos');
        }
        $data['status']   =true;
        return response([
            'data' =>  $data,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);


        $data['now'] = Carbon::now();
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::parse($request->get('from'));
            $data['to'] =  $to = Carbon::parse($request->get('to'));
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


        $active = 1;
        if ($active) {
            $data['all_pos'] = Pos::with(['withdrawalDetail' => function ($query) use ($active, $from, $to) {
                $query->whereHas('withdrawal', function (Builder $q1) use ($active, $from, $to) {
                    $q1->where('isDone', $active)
                        ->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
                });
            }])->with('posBack')->get();
        }


        $data['statisticals']['withdrawal']['money'] = 0;
        $data['statisticals']['withdrawal']['money_drawal'] = 0;
        $data['statisticals']['withdrawal']['fee_bank_money'] = 0;
        $data['statisticals']['withdrawal']['money_back'] = 0;

        // 
        $data['statisticals']['drawal']['money'] = 0;
        $data['statisticals']['drawal']['fee_bank_money'] = 0;
        $data['statisticals']['drawal']['money_back'] = 0;
        $data['statisticals']['drawal']['money_tranfer'] = 0;
        $data['statisticals']['drawal']['profit_money'] = 0;
        $data['statisticals']['drawal']['fee_user'] = 0;

        $data['statisticals']['pos_back_money'] = 0;
        $data['statisticals']['money_not_back_yet'] = 0;
        $data['statisticals']['profit_money_sub_fee_bank'] = 0;
        
        foreach ($data['all_pos'] as $key => $pos) {

            // Rút tiền
            $pos->drawal_statisticals = $drawal_statisticals = $pos->getAllMoneyDrawal($active, $from, $to);



            $pos->statisticals = $statisticals = $pos->getAllMoneyWithdrawal($active, $from, $to);

            $data['statisticals']['drawal']['money'] += $drawal_statisticals['money'];
            $data['statisticals']['drawal']['fee_bank_money'] += $drawal_statisticals['fee_bank_money'];
            $data['statisticals']['drawal']['money_back'] += $drawal_statisticals['money_back'];
            $data['statisticals']['drawal']['profit_money'] += $drawal_statisticals['profit_money'];
            // //Đáo hạn
            $moneyBack = $pos->getMoneyBack($active, $from, $to);
            $pos_back_money = $moneyBack['moneyback'];
            // // tổng tiền chưa về của máy pos =  tiền về - tiền  rút
            $pos->money_not_back_yet = $money_not_back_yet = $moneyBack['money_not_back_yet'];
            $data['statisticals']['withdrawal']['money'] += $statisticals['money'];
            $data['statisticals']['withdrawal']['money_drawal'] += $statisticals['money_drawal'];
            $data['statisticals']['withdrawal']['fee_bank_money'] += $statisticals['fee_bank_money'];
            $data['statisticals']['withdrawal']['money_back'] += ($statisticals['money_drawal'] - $statisticals['fee_bank_money']);
            $data['statisticals']['pos_back_money'] += $pos_back_money;
            $data['statisticals']['money_not_back_yet'] += $money_not_back_yet;

            // $moneyBack = $pos->getMoneyBack($active, $from, $to);

            $pos->pos_back_money =  $pos_back_money = $moneyBack['moneyback'];


        }

        // Lợi nhuận đã tính

 
        // Tổng tiền = tiền rút (đáo hạn) - phí ngân hàng(pos) + lợi tức

        // tiền về =
        $data['statisticals']['money_back'] = $data['statisticals']['withdrawal']['money_back'];
            // Số tiền thực tế;
        return response([
            'data' =>  $data,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);
        
    }
    
    public function indexGlobalDetail(DashboardRequest $request)
    {
        
        $data['now'] = Carbon::now();
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::parse($request->get('from'));
            $data['to'] =  $to = Carbon::parse($request->get('to'));
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
        $globalDetails = GlobalDetail::whereBetween('perDay', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->get();
        $data['totalTransactions'] = $globalDetails->sum('totalTransactions');
        $data['totalDrawals'] = $globalDetails->sum('totalDrawals');
        $data['totalCustomerNew'] = $globalDetails->sum('totalCustomerNew');
        $data['fee_ship'] = $globalDetails->sum('fee_ship');
        $data['expense'] = $globalDetails->sum('expense');
        $data['totalProfit'] = $globalDetails->sum('totalProfit');
        $data['status'] = true;
        
        if($data['to']>=$data['now'])
        {
                $drawals = Drawal::whereBetween('datetime', [$data['now']->format('Y-m-d') . " 00:00:00", $data['now']->format('Y-m-d') . " 23:59:59"])->with('details')->where('isDone', 1)->get();

                $withDrawals = Withdrawal::whereBetween('datetime', [$data['now']->format('Y-m-d') . " 00:00:00", $data['now']->format('Y-m-d') . " 23:59:59"])->with('withdrawalDetail')->where('isDone', 1)->get();
                
                $totalTransactions = $drawals->count() + $withDrawals->count();
                $totalDrawals = 0;
                $totalProfit = 0;
                $fee_ship = 0;
                $expense = 0;


                $expense = Expense::whereBetween('created_at', [$data['now']->format('Y-m-d') . " 00:00:00", $data['now']->format('Y-m-d') . " 23:59:59"])->sum('debitAmount');
                $expense -= Expense::whereBetween('created_at', [$data['now']->format('Y-m-d') . " 00:00:00", $data['now']->format('Y-m-d') . " 23:59:59"])->sum('creditAmount');
                $totalCustomerNew = Customer::whereBetween('created_at', [$data['now']->format('Y-m-d') . " 00:00:00", $data['now']->format('Y-m-d') . " 23:59:59"])->count();
                
                foreach($drawals as  $drawal) {
                    foreach($drawal->details as $detail) {
                        $totalDrawals +=  $detail->money;
                        $totalProfit += $detail->profit;
                    }
                    
                    $fee_ship += $drawal->fee_ship;
                }


                foreach($withDrawals as  $withDrawal) {

                    foreach($withDrawal->withdrawalDetail as $detail) {
                        $totalDrawals +=  $detail->money_drawal;
                        $totalProfit += $detail->profit;
                    }
                    $fee_ship += $withDrawal->fee_ship;
                }

                $data['totalTransactions'] += $totalTransactions;
                $data['totalDrawals'] += $totalDrawals;
                $data['totalCustomerNew'] += $totalCustomerNew;
                $data['fee_ship'] += $fee_ship;
                $data['expense'] += $expense;
                $data['totalProfit'] += $totalProfit;

            }

        return response([
            'data' =>  $data,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);



        // 
        $data['now'] = Carbon::now();
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::parse($request->get('from'));
            $data['to'] =  $to = Carbon::parse($request->get('to'));
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


        

        $data['from'] = $data['from']->startOfDay();
        $data['to'] =  $data['to']->endOfDay();

        $data['thu_chi'] = BankLog::where('isChecked', 1)->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->where('content_fix', 'THUCHI')->sum('debitAmount');
        $data['users'] = User::where('activeBank',true)->get();
        
        $data['investors'] = Customer::where('type',2)->sum('money');
        $data['total_money_plus'] = Customer::where('money','>',0)->where('type',1)->sum('money');
        $data['total_money_minus'] = Customer::where('money','<',0)->where('type',1)->sum('money');

        $data['profit_transaction'] = 0;
       

        $data['totalCustomer'] = Customer::all()->count();
        $data['totalCustomerNew'] = Customer::whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->count();
        $data['totalTransactions'] = Drawal::whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->where('isDone', 1)->count();

        $data['totalTransactions'] += Withdrawal::whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->where('isDone', 1)->count();

        
        $active = 1;
        if ($active) {
            $data['all_pos'] = Pos::with(['withdrawalDetail' => function ($query) use ($active, $from, $to) {
                $query->whereHas('withdrawal', function (Builder $q1) use ($active, $from, $to) {
                    $q1->where('isDone', $active)
                        ->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
                });
            }])->with('posBack')->get();
        }
        $data['fee_ship'] = Withdrawal::where('isDone', $active)
        ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])
        ->select('id','fee_ship')->sum('fee_ship');
        $data['fee_ship'] += Drawal::where('isDone', $active)
        ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])
        ->select('id','fee_ship')->sum('fee_ship');

        $data['statisticals']['withdrawal']['money'] = 0;
        $data['statisticals']['withdrawal']['money_drawal'] = 0;
        $data['statisticals']['withdrawal']['fee_bank_money'] = 0;
        $data['statisticals']['withdrawal']['money_back'] = 0;

        // 
        $data['statisticals']['drawal']['money'] = 0;
        $data['statisticals']['drawal']['fee_bank_money'] = 0;
        $data['statisticals']['drawal']['money_back'] = 0;
        $data['statisticals']['drawal']['money_tranfer'] = 0;
        $data['statisticals']['drawal']['profit_money'] = 0;
        $data['statisticals']['drawal']['fee_user'] = 0;

        $data['statisticals']['pos_back_money'] = 0;
        $data['statisticals']['money_not_back_yet'] = 0;
        $data['statisticals']['profit_money_sub_fee_bank'] = 0;
        $data['total_ckrt_comfirm'] = BankLog::where('isChecked', $active)->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->where(function ($query) {
            $query->where('content', 'like', '%CKRT%')->orWhere('content_fix', 'like', '%CKRT%');
        })->sum('debitAmount');

        $data['statisticals']['withdrawal']['profit_money'] = 0;

        foreach ($data['all_pos'] as $key => $pos) {

            // Rút tiền
            $pos->drawal_statisticals = $drawal_statisticals = $pos->getAllMoneyDrawal($active, $from, $to);


            
            $pos->statisticals = $statisticals = $pos->getAllMoneyWithdrawal($active, $from, $to);

            $data['statisticals']['drawal']['money'] += $drawal_statisticals['money'];
            $data['statisticals']['drawal']['fee_bank_money'] += $drawal_statisticals['fee_bank_money'];
            $data['statisticals']['drawal']['money_back'] += $drawal_statisticals['money_back'];
            $data['statisticals']['drawal']['profit_money'] += $drawal_statisticals['profit_money'];

            // //Đáo hạn
            $moneyBack = $pos->getMoneyBack($active, $from, $to);
            $pos_back_money = $moneyBack['moneyback'];
            // // tổng tiền chưa về của máy pos =  tiền về - tiền  rút
            $pos->money_not_back_yet = $money_not_back_yet = $moneyBack['money_not_back_yet'];
            $data['statisticals']['withdrawal']['money'] += $statisticals['money'];
            $data['statisticals']['withdrawal']['money_drawal'] += $statisticals['money_drawal'];
            $data['statisticals']['withdrawal']['profit_money'] += $statisticals['profit_money'];
            $data['statisticals']['withdrawal']['fee_bank_money'] += $statisticals['fee_bank_money'];
            $data['statisticals']['withdrawal']['money_back'] += ($statisticals['money_drawal'] - $statisticals['fee_bank_money']);
            $data['statisticals']['pos_back_money'] += $pos_back_money;
            $data['statisticals']['money_not_back_yet'] += $money_not_back_yet;
            $moneyBack = $pos->getMoneyBack($active, $from, $to);
            $pos->pos_back_money =  $pos_back_money = $moneyBack['moneyback'];

        }
        $data['totalProfit'] = $data['statisticals']['withdrawal']['profit_money'] +  $data['statisticals']['drawal']['profit_money'];
 
        // tiền về =
        $data['statisticals']['money_back'] = $data['statisticals']['withdrawal']['money_back'];
            // Số tiền thực tế;
        return response([
            'data' =>  $data,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);
    }


}
