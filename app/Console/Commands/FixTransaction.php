<?php

namespace App\Console\Commands;

use App\Models\BankLog\BankLog;
use App\Models\Customer\Customer;
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

class FixTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:fix';

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
        $users = User::where('autoPosBack',1)->whereHas('pos')->with('pos')->get();
        foreach ($users as $user) {
            
            foreach ($user->pos as $p) {
                $posConsignments = $p->posConsignment;
                foreach ($posConsignments  as $posConsignment) {
                        
                    if($posConsignment->getTotalMoney() == 0 ) {
                        $posConsignment->delete();
                    }
                    if ($posConsignment->getMoneyBack() == $posConsignment->getTotalMoney()) {
                        $posConsignment->isDone = 1;
                        $posConsignment->save();
                    } else {
                        
                        $money_need_back =  $posConsignment->getTotalMoney() - $posConsignment->getMoneyBack();
                        PosBack::create([
                            'name' => 'Auto add posBack Lô:'.$posConsignment->lo,
                            'money' => $money_need_back,
                            'pos_id' => $p->id,
                            'note' => 'Tự động chia tiền pos',
                            'lo' => $posConsignment->lo,
                            'active' => 1,
                        ]);
                    }
                }
            }

        }
    }

    public function excuteData($data, $user)
    {

    }
}
