<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Backend\Dashboard\DashboardRequest;
use App\Models\BankLog\BankLog;
use App\Models\Branch\Branch;
use App\Models\Customer\Customer;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Drawal\Drawal;
use App\Models\DrawalDetail\DrawalDetail;
use App\Models\Pos\Pos;
use App\Models\Users\User;
use App\Models\Withdrawal\Withdrawal;
use App\Models\WithdrawalDetail\WithdrawalDetail;
use Artisan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class DashboardController.
 */
class Dashboard1Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */

    public function mbCheck() {
        Artisan::call('bank:MBCheck');
        return redirect()->route('admin.bankLog.index');
    }

    public function indexBranch(Request $request) {
        $data['now'] = Carbon::now();
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::createFromFormat('d/m/Y', $request->from);
            $data['to'] =  $to = Carbon::createFromFormat('d/m/Y', $request->to);
            
        } else {
            $data['from'] =  $from = Carbon::now();
            $data['to'] =  $to = Carbon::now();
        }
        $data['branchs'] = Branch::get();
        if(!$data['branch'] = Branch::find($request->branch_id)) {
           $data['branch'] = Branch::first();
        }
        $data['total'] = 0;
        $branch_id = $data['branch']->id;
        if($data['branch']) {
            $data['total'] += DrawalDetail::whereHas('drawal', function (Builder $query) use ($from, $to, $branch_id) {
                $query->where('isDone', 1)->where('branch_id',$branch_id)
                    ->whereBetween('datetime', [$from->startOfDay(), $to->endOfDay()]);
            })->sum('money');

            $data['total'] += WithdrawalDetail::whereHas('withdrawal', function (Builder $query) use ($from, $to, $branch_id) {
                $query->where('isDone', 1)->where('branch_id',$branch_id)
                    ->whereBetween('datetime', [$from->startOfDay(), $to->endOfDay()]);
            })->sum('money_drawal');
        }
        return view('backend.transactionBranch', $data);

    }
    
    public function indexGlobal(Request $request) {
        $data1['users'] = User::where('activeBank',true)->get();
        $data1['settings'] = settings()->all();
        return view('backend.transactionGlobal',$data1);

    }

    public function fixTransaction(Request $request) {

        // settings()->set([
        //     'total_profit' => Expense::sum('debitAmount'),
        // ]);
        
         
        // $p = PosConsignment::find(466);
        // dd($p->getTotalMoney(),$p->getMoneyBack());
        // $users = User::where('autoPosBack',1)->whereHas('pos')->with('pos')->get();
        // foreach ($users as $user) {
            
        //     foreach ($user->pos as $p) {
        //         $posConsignments = $p->posConsignment;
        //         foreach ($posConsignments  as $posConsignment) {
                        
        //             if($posConsignment->getTotalMoney() == 0 ) {
        //                 $posConsignment->delete();
        //             }
        //             if ($posConsignment->getMoneyBack() == $posConsignment->getTotalMoney()) {
        //                 $posConsignment->isDone = 1;
        //                 $posConsignment->save();
        //             } else {
                        
        //                 $money_need_back =  $posConsignment->getTotalMoney() - $posConsignment->getMoneyBack();
        //                 PosBack::create([
        //                     'name' => 'Auto add posBack Lô:'.$posConsignment->lo,
        //                     'money' => $money_need_back,
        //                     'pos_id' => $p->id,
        //                     'note' => 'Tự động chia tiền pos',
        //                     'lo' => $posConsignment->lo,
        //                     'active' => 1,
        //                 ]);
        //             }
        //         }
        //     }

        // }

        // fixx fund
    //    $money = 0;
    //    $funds = FundTransaction::where('user_id',32)->orderby('id','asc')->get();
    //    foreach($funds as  $fund) {
    //      $debitAmount = $fund->debitAmount;
    //      $creditAmount = $fund->creditAmount;
    //      if($debitAmount > 0) {
    //         $fund->money_before = $money;
    //         $fund->money_after = $money - $debitAmount;
    //         $fund->save();
    //         $money =  $fund->money_after;
    //      }
    //      else {
    //         $fund->money_before = $money;
    //         $fund->money_after = $money + $creditAmount;
    //         $fund->save();
    //         $money =  $fund->money_after;
    //      }
    //    }

    //    $money = 0;
    //    $funds = FundTransaction::where('user_id',5)->orderby('id','asc')->get();
    //    foreach($funds as  $fund) {
    //      $debitAmount = $fund->debitAmount;
    //      $creditAmount = $fund->creditAmount;
    //      if($debitAmount > 0) {
    //         $fund->money_before = $money;
    //         $fund->money_after = $money - $debitAmount;
    //         $fund->save();
    //         $money =  $fund->money_after;
    //      }
    //      else {
    //         $fund->money_before = $money;
    //         $fund->money_after = $money + $creditAmount;
    //         $fund->save();
    //         $money =  $fund->money_after;
    //      }
    //    }


    }

    public function pos(DashboardRequest $request)
    {
        
        $data['users'] = User::where('activeBank',true)->get();
        $data['now'] = Carbon::now();
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::createFromFormat('d/m/Y', $request->from);
            $data['to'] =  $to = Carbon::createFromFormat('d/m/Y', $request->to);
        } else {
            $data['from'] =  $from = Carbon::now();
            $data['to'] =  $to = Carbon::now();
        }
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

        $data['statisticals']['pos_back'] = 0;
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
            $pos->pos_back =  $pos_back = $moneyBack['moneyback'];
            // // tổng tiền chưa về của máy pos =  tiền về - tiền  rút
            $pos->money_not_back_yet = $money_not_back_yet = $moneyBack['money_not_back_yet'];
            $data['statisticals']['withdrawal']['money'] += $statisticals['money'];
            $data['statisticals']['withdrawal']['money_drawal'] += $statisticals['money_drawal'];
            $data['statisticals']['withdrawal']['fee_bank_money'] += $statisticals['fee_bank_money'];
            $data['statisticals']['withdrawal']['money_back'] += ($statisticals['money_drawal'] - $statisticals['fee_bank_money']);
            $data['statisticals']['pos_back'] += $pos_back;
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

        return view('backend.pos', $data);
    }

    public function index(DashboardRequest $request)
    {
        
        $data['users'] = User::where('activeBank',true)->get();
        $data['investors'] = Customer::where('type',2)->sum('money');
        $data['total_money_plus'] = Customer::where('money','>',0)->where('type',1)->sum('money');
        $data['total_money_minus'] = Customer::where('money','<',0)->where('type',1)->sum('money');

        $data['profit_transaction'] = 0;
        $data['now'] = Carbon::now();
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::createFromFormat('d/m/Y', $request->from);
            $data['to'] =  $to = Carbon::createFromFormat('d/m/Y', $request->to);
            
        } else {
            $data['from'] =  $from = Carbon::now();
            $data['to'] =  $to = Carbon::now();
        }

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

        $data['statisticals']['pos_back'] = 0;
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
            $pos->pos_back =  $pos_back = $moneyBack['moneyback'];
            // // tổng tiền chưa về của máy pos =  tiền về - tiền  rút
            $pos->money_not_back_yet = $money_not_back_yet = $moneyBack['money_not_back_yet'];


            $data['statisticals']['withdrawal']['money'] += $statisticals['money'];
            $data['statisticals']['withdrawal']['money_drawal'] += $statisticals['money_drawal'];
            $data['statisticals']['withdrawal']['fee_bank_money'] += $statisticals['fee_bank_money'];
            $data['statisticals']['withdrawal']['money_back'] += ($statisticals['money_drawal'] - $statisticals['fee_bank_money']);
            $data['statisticals']['pos_back'] += $pos_back;
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

        return view('backend.dashboard', $data);
    }

    public function transaction(Request $request)
    {

        $data['branchs'] = Branch::get();
        $active = 1;
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::createFromFormat('d/m/Y', $request->from);
            $data['to'] =  $to = Carbon::createFromFormat('d/m/Y', $request->to);
        } else {
            $data['from'] =  $from = Carbon::now();
            $data['to'] =  $to = Carbon::now();
        }

        $detail1 =  DrawalDetail::whereHas('drawal', function (Builder $q1) use ($active, $from, $to) {
            $q1->where('isDone', $active)
                ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
        })->get();
        $detail2 =  WithdrawalDetail::whereHas('withdrawal', function (Builder $q1) use ($active, $from, $to) {
            $q1->where('isDone', $active)
                ->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
        })->get();
        $array = [];
        $array2 = [];
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
            $array[$d->lo][$d->bill]['stt'] = $d->drawal->stt;
            $array[$d->lo][$d->bill]['lo'] = $d->lo;
            $array[$d->lo][$d->bill]['bill'] = $d->bill;
            $array[$d->lo][$d->bill]['money'] = $d->money;
            $array[$d->lo][$d->bill]['customerCard'] = $d->customerCard ? $d->customerCard->name : '';
            $array[$d->lo][$d->bill]['detail'] = $d;
            $array[$d->lo][$d->bill]['money_back'] = $d->money_back;
            $array[$d->lo][$d->bill]['fee_ship'] = $d->fee_ship;
            $array[$d->lo][$d->bill]['fee_customer'] = $d->drawal->fee_customer;
            $array[$d->lo][$d->bill]['profit'] = $d->profit;
            $array[$d->lo][$d->bill]['fee_bank'] = $d->fee_bank;
            $array[$d->lo][$d->bill]['fee_bank_money'] = $d->fee_bank_money;
            $array[$d->lo][$d->bill]['fee_customer_money'] = $d->drawal->fee_customer*$d->money/100;
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
            $array[$d->lo][$d->bill]['stt'] = $d->withdrawal->stt;
            $array[$d->lo][$d->bill]['lo'] = $d->lo;
            $array[$d->lo][$d->bill]['bill'] = $d->bill;
            $array[$d->lo][$d->bill]['money'] = $d->money_drawal;
            $array[$d->lo][$d->bill]['customerCard'] = $d->withdrawal->customerCard ? $d->withdrawal->customerCard->name : '';
            $array[$d->lo][$d->bill]['detail'] = $d;
            $array[$d->lo][$d->bill]['money_back'] = $d->money_back;
            $array[$d->lo][$d->bill]['fee_ship'] = $d->fee_ship;
            $array[$d->lo][$d->bill]['fee_customer'] = $d->withdrawal->fee_customer;
            $array[$d->lo][$d->bill]['profit'] = $d->profit;
            $array[$d->lo][$d->bill]['fee_bank'] = $d->fee_bank;
            $array[$d->lo][$d->bill]['fee_bank_money'] =  $d->fee_bank*$d->money_drawal/100;
            $array[$d->lo][$d->bill]['fee_customer_money'] = $d->withdrawal->fee_customer*$d->money_drawal/100;
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


        return view('backend.transaction', $data);

    }

    public function transactionsNotDone(Request $request)
    {
        $data['branchs'] = Branch::get();
        $data['money'] = 0;
        $data['sl'] = 0;
        $active = 1;
        if ($request->from && $request->to) {
            $data['from'] =  $from = Carbon::createFromFormat('d/m/Y', $request->from);
            $data['to'] =  $to = Carbon::createFromFormat('d/m/Y', $request->to);
        } else {
            $data['from'] =  $from = Carbon::now();
            $data['to'] =  $to = Carbon::now();
        }

        $detail1 =  DrawalDetail::whereHas('drawal', function (Builder $q1) use ($active, $from, $to) {
            $q1->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
        })->get();
        $detail2 =  WithdrawalDetail::whereHas('withdrawal', function (Builder $q1) use ($active, $from, $to) {
            $q1->whereBetween('datetime', [$from->format('Y-m-d') . " 00:00:00", $to->format('Y-m-d') . " 23:59:59"]);
        })->get();
        $array = [];
        $array2 = [];
        $data['total_pos_back'] = 0;
        $data['total_money'] =0;
        $data['money'] = 0;
        $data['sl'] = 0;
        $data['total_pos_back'] =0;
        $data['total_money'] =0;
        $data['total_fee_ship'] =0;
        $data['total_fee_customer'] =0;
        $data['total_profit'] =0;
        $data['total_fee_bank_money'] =0;
        foreach ($detail1 as $d) {
            // dd($d);
            if(!isset($data['posLists'][$d->pos_id])) {
                $data['posLists'][$d->pos_id] = 0;
            }
            $data['posLists'][$d->pos_id]+= $d->profit;
            $data['sl'] ++;
            $data['money'] += $d->profit;
            $array[$d->lo][$d->bill]['stt'] = $d->drawal->stt;
            $array[$d->lo][$d->bill]['lo'] = $d->lo;
            $array[$d->lo][$d->bill]['bill'] = $d->bill;
            $array[$d->lo][$d->bill]['money'] = $d->money;
            $array[$d->lo][$d->bill]['customerCard'] = $d->customerCard ? $d->customerCard->name : '';
            $array[$d->lo][$d->bill]['code'] = '';
            $array[$d->lo][$d->bill]['money_back'] = $d->money_back;
            $array[$d->lo][$d->bill]['fee_ship'] = $d->fee_ship;
            $array[$d->lo][$d->bill]['fee_customer'] = $d->drawal->fee_customer;
            $array[$d->lo][$d->bill]['profit'] = $d->profit;
            $array[$d->lo][$d->bill]['fee_bank'] = $d->fee_bank;
            $array[$d->lo][$d->bill]['fee_bank_money'] = $d->fee_bank_money;
            $array[$d->lo][$d->bill]['fee_customer_money'] = $d->drawal->fee_customer*$d->money/100;
            $array[$d->lo][$d->bill]['detail'] = $d;
            $data['total_pos_back'] +=$d->money_back;
            $data['total_money'] +=$d->money;
            $data['total_fee_ship'] +=$d->fee_ship;
            $data['total_fee_customer'] +=$d->fee_customer;
            $data['total_profit'] +=$d->profit;
            $data['total_fee_bank_money'] +=$d->fee_bank_money;

        }
        foreach ($detail2 as $d) {
            // dd($d);
            if(!isset($data['posLists'][$d->pos_id])) {
                $data['posLists'][$d->pos_id] = 0;
            }
            $data['posLists'][$d->pos_id]+= $d->profit;
            $data['sl'] ++;
            $data['money'] += $d->profit;
            $array[$d->lo][$d->bill]['stt'] = $d->withdrawal->stt;

            $array[$d->lo][$d->bill]['lo'] = $d->lo;
            $array[$d->lo][$d->bill]['bill'] = $d->bill;
            $array[$d->lo][$d->bill]['money'] = $d->money_drawal;
            $array[$d->lo][$d->bill]['customerCard'] = $d->withdrawal->customerCard ? $d->withdrawal->customerCard->name : '';
            $array[$d->lo][$d->bill]['code'] = '';
            $array[$d->lo][$d->bill]['money_back'] = $d->money_back;
            $array[$d->lo][$d->bill]['fee_ship'] = $d->fee_ship;
            $array[$d->lo][$d->bill]['fee_customer'] = $d->withdrawal->fee_customer;
            $array[$d->lo][$d->bill]['profit'] = $d->profit;
            $array[$d->lo][$d->bill]['fee_bank'] = $d->fee_bank;
            $array[$d->lo][$d->bill]['fee_bank_money'] =  $d->fee_bank*$d->money_drawal/100;
            $array[$d->lo][$d->bill]['fee_customer_money'] = $d->withdrawal->fee_customer*$d->money_drawal/100;
            $array[$d->lo][$d->bill]['detail'] = $d;
            $data['total_pos_back'] +=$d->money_back;
            $data['total_money'] +=$d->money;
            $data['total_fee_ship'] +=$d->fee_ship;
            $data['total_fee_customer'] +=$d->fee_customer;
            $data['total_profit'] +=$d->profit;
            $data['total_fee_bank_money'] +=$d->fee_bank_money;

        }
        ksort($array);
        $new_array = [];
        
        foreach($array as  $key => $a) {
            $b = $a;
            ksort($a);
            $new_array[$key] = $a;
        }

        $data['lists'] = $array;
        // dd($array);
        return view('backend.transaction', $data);

    }
}
