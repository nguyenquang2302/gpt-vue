<?php

namespace App\Console\Commands;

use App\Models\BankLog\BankLog;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Drawal\Drawal;
use App\Models\DrawalDetail\DrawalDetail;
use App\Models\Expense\Expense;
use App\Models\FundTransaction\FundTransaction;
use App\Models\GlobalDetail\GlobalDetail;
use App\Models\Pos\Pos;
use App\Models\PosBack\PosBack;
use App\Models\Users\User;
use App\Models\Withdrawal\Withdrawal;
use App\Models\WithdrawalDetail\WithdrawalDetail;
use App\Services\CustomerTransactionService;
use App\Services\ExpenseService;
use App\Services\FundTransactionService;
use Carbon\Carbon;
use Google\Service\Monitoring\Custom;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class globalDetailCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'global:detail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Thống kê chi tiết';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $drawal  = Drawal::orderBy('datetime','asc')->first();
        $withDrawal  = Withdrawal::orderBy('datetime','asc')->first();
        $start = ($drawal->datetime > $withDrawal->datetime) ? $withDrawal->datetime : $drawal->datetime;
        
        $globalDetail = GlobalDetail::whereDate('perDay',$start)->first();
        do {
            $globalDetail = GlobalDetail::whereDate('perDay',$start)->first();
            if(!$globalDetail) {

                $drawals = Drawal::whereBetween('datetime', [$start->format('Y-m-d') . " 00:00:00", $start->format('Y-m-d') . " 23:59:59"])->with('details')->where('isDone', 1)->get();

                $withDrawals = Withdrawal::whereBetween('datetime', [$start->format('Y-m-d') . " 00:00:00", $start->format('Y-m-d') . " 23:59:59"])->with('withdrawalDetail')->where('isDone', 1)->get();
                
                $totalTransactions = $drawals->count() + $withDrawals->count();
                $totalDrawals = 0;
                $totalProfit = 0;
                $fee_ship = 0;
                $expense = 0;


                $expense = Expense::whereBetween('created_at', [$start->format('Y-m-d') . " 00:00:00", $start->format('Y-m-d') . " 23:59:59"])->sum('debitAmount');
                $expense -= Expense::whereBetween('created_at', [$start->format('Y-m-d') . " 00:00:00", $start->format('Y-m-d') . " 23:59:59"])->sum('creditAmount');
                $totalCustomerNew = Customer::whereBetween('created_at', [$start->format('Y-m-d') . " 00:00:00", $start->format('Y-m-d') . " 23:59:59"])->count();
                
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
                    $fee_ship += $drawal->fee_ship;
                }

                $globalDetail =  new GlobalDetail();
                $globalDetail->totalTransactions = $totalTransactions;
                $globalDetail->totalDrawals = $totalDrawals;
                $globalDetail->totalProfit = $totalProfit;
                $globalDetail->fee_ship = $fee_ship;
                $globalDetail->expense = $expense;
                $globalDetail->totalCustomerNew = $totalCustomerNew;
                $globalDetail->perDay = $start;
                $globalDetail->save();
            }
            $start->addDay();
        } while(!$start->isToday());
    
    }

    public function excuteData($data, $user)
    {

    }
}
