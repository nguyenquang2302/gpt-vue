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

        foreach ($detail1 as $d) {
            if(!isset($data['posLists'][$d->pos_id])) {
                $data['posLists'][$d->pos_id]['profit'] = 0;
                $data['posLists'][$d->pos_id]['name'] = $d->pos->name;
            }
            $data['posLists'][$d->pos_id]['profit']+= $d->profit;
            $data['sl'] ++;
            $data['money'] += $d->profit;
            $d->drawal->stt = $d->drawal->stt??0;
            $key =  str_replace('_','',$d->pos_id.$d->lo.$d->bill);

            $array[$key]['pos_name'] = $d->pos->name;
            $array[$key]['stt'] = $d->drawal->stt;
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
            if(!isset($data['posLists'][$d->pos_id])) {
                $data['posLists'][$d->pos_id]['profit'] = 0;
                $data['posLists'][$d->pos_id]['name'] = $d->pos->name;
            }
            $data['posLists'][$d->pos_id]['profit']+= $d->profit;
            $data['sl'] ++;
            $data['money'] += $d->profit;
            $d->withdrawal->stt = $d->withdrawal->stt??0;
            $key =  str_replace('_','',$d->pos_id.$d->lo.$d->bill);

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
                    $data['expences']  = Expense::with('fundCategory')->get();
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
        if(!isset($data['expences']))
            $data['expences']  = Expense::with('fundCategory')->where('created_at', [$from->startOfDay(), $to->endOfDay()])->get();
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

        $withdrawalDetails = WithdrawalDetail::whereHas('withdrawal', function (Builder $query) use ($from, $to) {
                $query->where('isDone', 1)
                    ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
            })->get();
        $drawalDetails =  DrawalDetail::whereHas('drawal', function (Builder $query) use ($from, $to) {
                $query->where('isDone', 1)
                    ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
            })->get();
       
        foreach($drawalDetails as $drawalDetail1) {
            
            $drawalDetail1->profit = ($drawalDetail1->money* $drawalDetail1->drawal->fee_customer/100) - ($drawalDetail1->money* $drawalDetail1->fee_bank/100) ;
            $drawalDetail1->save();
        }

        foreach($withdrawalDetails as $withdrawalDetail) {
            
            $withdrawalDetail->profit = ($withdrawalDetail->money_drawal * $withdrawalDetail->withDrawal->fee_customer)/100 - ($withdrawalDetail->money_drawal *$withdrawalDetail->fee_bank)/100;
            $withdrawalDetail->save();

        }
        $data['totalProfit'] =  $withdrawalDetails->sum('profit');
        $data['totalProfit'] += $drawalDetails->sum('profit');

        $data['statisticals']['withdrawal']['money'] = 0;
        $data['statisticals']['withdrawal']['money_drawal'] = 0;
        $data['statisticals']['withdrawal']['fee_bank_money'] = 0;
        $data['statisticals']['withdrawal']['money_back'] = 0;

        // 
        $data['statisticals']['drawal']['money'] = 0;
        $data['statisticals']['drawal']['money_drawal'] = 0;
        $data['statisticals']['drawal']['fee_bank_money'] = 0;
        $data['statisticals']['drawal']['money_back'] = 0;
        $data['statisticals']['drawal']['money_tranfer'] = 0;
        $data['statisticals']['drawal']['profit_money'] = 0;
        $data['statisticals']['drawal']['fee_user'] = 0;

        $data['statisticals']['pos_back_money'] = 0;
        $data['statisticals']['money_not_back_yet'] = 0;
        $data['statisticals']['profit_money_sub_fee_bank'] = 0;
        $data['statisticals']['drawal']['money_drawal']  = CustomerTransaction::where('source', 'CKRT')->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->sum('money');

        foreach ($data['all_pos'] as $key => $pos) {

            // Rút tiền
            $pos->drawal_statisticals = $drawal_statisticals = $pos->getAllMoneyDrawal($active, $from, $to);
            $pos->statisticals = $statisticals = $pos->getAllMoneyWithdrawal($active, $from, $to);

            $data['statisticals']['drawal']['money'] += $drawal_statisticals['money'];
            $data['statisticals']['drawal']['fee_bank_money'] += $drawal_statisticals['fee_bank_money'];
            $data['statisticals']['drawal']['money_back'] += $drawal_statisticals['money_back'];
            $data['statisticals']['drawal']['profit_money'] += $drawal_statisticals['profit_money'];
            // dd($statisticals['profit_money'], $drawal_statisticals['profit_money']);

            // //Đáo hạn
            $moneyBack = $pos->getMoneyBack($active, $from, $to);
            $pos_money_money =  $pos_back_money = $moneyBack['moneyback'];
            // // tổng tiền chưa về của máy pos =  tiền về - tiền  rút
            $pos->money_not_back_yet = $money_not_back_yet = $moneyBack['money_not_back_yet'];
            $data['statisticals']['withdrawal']['money'] += $statisticals['money'];
            $data['statisticals']['withdrawal']['money_drawal'] += $statisticals['money_drawal'];
            $data['statisticals']['withdrawal']['fee_bank_money'] += $statisticals['fee_bank_money'];
            $data['statisticals']['withdrawal']['money_back'] += ($statisticals['money_drawal'] - $statisticals['fee_bank_money']);
            $data['statisticals']['pos_back_money'] += $pos_back_money;
            $data['statisticals']['money_not_back_yet'] += $money_not_back_yet;
            $moneyBack = $pos->getMoneyBack($active, $from, $to);
            $pos->pos_back_money =  $pos_back_money = $moneyBack['moneyback'];

        }

        // Lợi nhuận đã tính

        $data['statisticals']['money_drawal'] = $data['statisticals']['withdrawal']['money_drawal'];
        +$data['statisticals']['drawal']['money_drawal'];
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


        $data['users'] = User::where('activeBank',true)->get();
        
        $data['investors'] = Customer::where('type',2)->sum('money');
        $data['total_money_plus'] = Customer::where('money','>',0)->where('type',1)->sum('money');
        $data['total_money_minus'] = Customer::where('money','<',0)->where('type',1)->sum('money');

        $data['profit_transaction'] = 0;
       

        $data['totalCustomer'] = Customer::all()->count();
        $data['totalCustomerNew'] = Customer::whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->count();
        $data['totalTransactions'] = Drawal::whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->where('isDone', 1)->count();

        $data['totalTransactions'] += Withdrawal::whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->where('isDone', 1)->count();
        // $msb = new MSBService('123','456');
        // dd($msb->sendSms());
        $customers = Customer::all();
        $data['money_debit'] = 0;
        foreach ($customers as $customer) {
            $transactions = $customer->customerTransactions()->get();
            $money = $transactions->sum('money');
            if ($money > 0) {
                $data['money_debit'] += $money;
            }
        }

        $active = 1;
        if ($active) {
            $data['all_pos'] = Pos::with(['withdrawalDetail' => function ($query) use ($active, $from, $to) {
                $query->whereHas('withdrawal', function (Builder $q1) use ($active, $from, $to) {
                    $q1->where('isDone', $active)
                        ->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
                });
            }])->with('posBack')->get();
            $withdrawal = Withdrawal::where('isDone', $active)
                ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])
                ->get();
            $drawal = Drawal::where('isDone', $active)
                ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])
                ->get();

            $bankLogs = BankLog::where('isChecked', $active)->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->get();
            $data['fee_ship'] = $drawal->sum('fee_ship') + $withdrawal->sum('fee_ship');
            $data['thu_chi'] = BankLog::where('isChecked', $active)->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->where('content_fix', 'THUCHI')->sum('debitAmount');
            $data['total_ckrt_comfirm'] = BankLog::where('isChecked', $active)->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->where(function ($query) {
                $query->where('content', 'like', '%CKRT%')->orWhere('content_fix', 'like', '%CKRT%');
            })
                ->sum('debitAmount');
        }

        $withdrawalDetails = WithdrawalDetail::whereHas('withdrawal', function (Builder $query) use ($from, $to) {
                $query->where('isDone', 1)
                    ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
            })->get();
        $drawalDetails =  DrawalDetail::whereHas('drawal', function (Builder $query) use ($from, $to) {
                $query->where('isDone', 1)
                    ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
            })->get();
       
        foreach($drawalDetails as $drawalDetail1) {
            
            $drawalDetail1->profit = ($drawalDetail1->money* $drawalDetail1->drawal->fee_customer/100) - ($drawalDetail1->money* $drawalDetail1->fee_bank/100) ;
            $drawalDetail1->save();
        }

        foreach($withdrawalDetails as $withdrawalDetail) {
            
            $withdrawalDetail->profit = ($withdrawalDetail->money_drawal * $withdrawalDetail->withDrawal->fee_customer)/100 - ($withdrawalDetail->money_drawal *$withdrawalDetail->fee_bank)/100;
            $withdrawalDetail->save();

        }
        $data['totalProfit'] =  $withdrawalDetails->sum('profit');
        $data['totalProfit'] += $drawalDetails->sum('profit');

        $data['statisticals']['withdrawal']['money'] = 0;
        $data['statisticals']['withdrawal']['money_drawal'] = 0;
        $data['statisticals']['withdrawal']['fee_bank_money'] = 0;
        $data['statisticals']['withdrawal']['money_back'] = 0;

        // 
        $data['statisticals']['drawal']['money'] = 0;
        $data['statisticals']['drawal']['money_drawal'] = 0;
        $data['statisticals']['drawal']['fee_bank_money'] = 0;
        $data['statisticals']['drawal']['money_back'] = 0;
        $data['statisticals']['drawal']['money_tranfer'] = 0;
        $data['statisticals']['drawal']['profit_money'] = 0;
        $data['statisticals']['drawal']['fee_user'] = 0;

        $data['statisticals']['pos_back_money'] = 0;
        $data['statisticals']['money_not_back_yet'] = 0;
        $data['statisticals']['profit_money_sub_fee_bank'] = 0;
        $data['statisticals']['profit'] = $withdrawal->sum('profit_money') + $drawal->sum('profit_money');
        $data['statisticals']['drawal']['money_drawal']  = CustomerTransaction::where('source', 'CKRT')->whereBetween('created_at', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"])->sum('money');

        foreach ($data['all_pos'] as $key => $pos) {
            // Rút tiền
            $pos->drawal_statisticals = $drawal_statisticals = $pos->getAllMoneyDrawal($active, $from, $to);
            $pos->statisticals = $statisticals = $pos->getAllMoneyWithdrawal($active, $from, $to);

            $data['statisticals']['drawal']['money'] += $drawal_statisticals['money'];
            $data['statisticals']['drawal']['fee_bank_money'] += $drawal_statisticals['fee_bank_money'];
            $data['statisticals']['drawal']['money_back'] += $drawal_statisticals['money_back'];
            $data['statisticals']['drawal']['profit_money'] += $drawal_statisticals['profit_money'];
            // dd($statisticals['profit_money'], $drawal_statisticals['profit_money']);
            $data['statisticals']['profit'] += $statisticals['profit_money'] + $drawal_statisticals['profit_money'];

            // //Đáo hạn
            $moneyBack = $pos->getMoneyBack($active, $from, $to);
            $pos->pos_back_money =  $pos_back_money = $moneyBack['moneyback'];
            // // tổng tiền chưa về của máy pos =  tiền về - tiền  rút
            $pos->money_not_back_yet = $money_not_back_yet = $moneyBack['money_not_back_yet'];


            $data['statisticals']['withdrawal']['money'] += $statisticals['money'];
            $data['statisticals']['withdrawal']['money_drawal'] += $statisticals['money_drawal'];
            $data['statisticals']['withdrawal']['fee_bank_money'] += $statisticals['fee_bank_money'];
            $data['statisticals']['withdrawal']['money_back'] += ($statisticals['money_drawal'] - $statisticals['fee_bank_money']);
            $data['statisticals']['pos_back_money'] += $pos_back_money;
            $data['statisticals']['money_not_back_yet'] += $money_not_back_yet;
        }



        // Lợi nhuận đã tính

        $data['statisticals']['money_drawal'] = $data['statisticals']['withdrawal']['money_drawal'];
        +$data['statisticals']['drawal']['money_drawal'];
        // Tổng tiền = tiền rút (đáo hạn) - phí ngân hàng(pos) + lợi tức

        // tiền về =
        $data['statisticals']['money_back'] = $data['statisticals']['withdrawal']['money_back'];
            // Số tiền thực tế

        ;

        $data['money_auth'] = -$data['total_money_minus'] - $data['total_money_plus'] +  $data['statisticals']['money_not_back_yet'];
        foreach($data['users'] as $user) {
            if($fund_transaction = $user->fundTransaction()->orderBy('id','desc')->first() ) {
                $data['money_auth']  += $fund_transaction->money_after;
            }
        }

        return response([
            'data' =>  $data,
            'message' => 'Danh sách thành công'
        ], Response::HTTP_OK);
    }


}