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

class caculatorTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caculator:total';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $total_profit = settings()->get('total_profit',0);
        $total_profit =  WithdrawalDetail::sum('profit');
        $total_profit += DrawalDetail::sum('profit');

        $total_ship = 0;
        $total_ship =  Withdrawal::sum('fee_ship');
        $total_ship += Drawal::sum('fee_ship');

        $data['all_pos'] = Pos::with(['withdrawalDetail' => function ($query) {
            $query->whereHas('withdrawal', function (Builder $q1){
                $q1->where('isDone', 1);
            });
        }])->with('posBack')->get();
        // settings()->set([
        //     'total_expense' => Expense::sum('debitAmount'),
        // ]);
        $money_not_back_yet = 0; // Tổng tiền pos chưa về
        foreach($data['all_pos'] as $pos) {
            $moneyBack = $pos->getTotalMoneyBack();
            $money_not_back_yet += $moneyBack['money_not_back_yet'];
        }
        $total_banklogs_debit = BankLog::where('isChecked',0)->sum('debitAmount');
        $total_banklogs_credit = BankLog::where('isChecked',0)->sum('creditAmount');
        $data['customer_cart_save'] = CustomerCard::where('save',1)->count();
       
        

        $data['total_money_plus'] = Customer::where('money','>',0)->where('type',1)->sum('money');
        $data['total_money_minus'] = Customer::where('money','<',0)->where('type',1)->sum('money');
        $data['users'] = User::where('activeBank',true)->get();
        // $data['money_auth'] = -$data['total_money_minus'] - $data['total_money_plus']+$data['settings']->get('money_not_back_yet');
        $data['money_auth'] = $money_not_back_yet;
        $data['real_money']= 0;
        foreach($data['users'] as $user) {
            if($fund_transaction = $user->fundTransaction()->orderBy('id','desc')->first() ) {
                $data['real_money'] += $fund_transaction->money_after;
                $data['money_auth']  += $fund_transaction->money_after;
            }
        }
        // tổng  chưa về + đang giữ  + đầu tư
        $data['money_auth_2'] = $data['money_auth'] - $data['total_money_minus'] - $data['total_money_plus'] + settings()->get('total_expense2',0);
        $data['totalTransactions'] = Drawal::where('isDone', 1)->count();

        $data['totalTransactions'] += Withdrawal::where('isDone', 1)->count();
        settings()->set([
            'customer_cart_save' => $data['customer_cart_save'],
            'total_profit' => $total_profit,
            'money_not_back_yet' =>  $money_not_back_yet,
            'total_expense' => Expense::whereHas('fundCategory', function($q){
                $q->where('type',0);
            })->where('debitAmount','>','0')->sum('debitAmount') - Expense::whereHas('fundCategory', function($q){
                $q->where('type',0);
            })->where('creditAmount','>','0')->sum('creditAmount'),
            
            'total_expense2' => Expense::whereHas('fundCategory', function($q){
                $q->where('type',1);
            })->sum('debitAmount')- Expense::whereHas('fundCategory', function($q){
                $q->where('type',1);
            })->where('creditAmount','>','0')->sum('creditAmount'),
            'total_banklogs_debit' => $total_banklogs_debit,
            'total_banklogs_credit' => $total_banklogs_credit,
            'total_ship' => $total_ship,
            'investors' => Customer::where('type',2)->sum('money'),
            'money_auth' => $data['money_auth'],
            'money_auth_2' => $data['money_auth_2'],
            'total_money_plus' => $data['total_money_plus'],
            'total_money_minus'=> $data['total_money_minus'],
            'totalTransactions' => $data['totalTransactions'],
        ]);
    }

    public function excuteData($data, $user)
    {

    }
}
