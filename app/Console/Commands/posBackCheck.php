<?php

namespace App\Console\Commands;

use App\Models\FundTransaction\FundTransaction;
use App\Models\PosBack\PosBack;
use App\Models\Users\User;
use App\Services\FundTransactionService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class posBackCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posback:check';

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
            $time_check_pos_back = settings()->get('time_check_pos_back', 0);
            if ($time_check_pos_back) {
                $time_check_pos_back_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $time_check_pos_back);
            } else {
                $time_check_pos_back_carbon = Carbon::now()->subHour(5);
            }
            $now = Carbon::now();
            $caculator_minutes = $time_check_pos_back_carbon->diffInMinutes($now);
            if ($caculator_minutes >= 4) {
                settings()->set([
                    'time_check_pos_back' => Carbon::now()
                ]);
            try {

                DB::beginTransaction();

                $users = User::where('autoPosBack',1)->whereHas('pos')->with('pos')->get();
                foreach ($users as $user) {
                    
                    foreach ($user->pos as $p) {
                        $posConsignments = $p->posConsignment()->orderBy('id','asc')->get();
                        foreach ($posConsignments  as $posConsignment) {
                            
                            if ($posConsignment->getMoneyBack() == $posConsignment->getTotalMoney()) {
                                $posConsignment->isDone = 1;
                                $posConsignment->save();
                            
                            } if($posConsignment->getMoneyBack() > $posConsignment->getTotalMoney()) {
                                //  chưa xử lý
                            }
                            else {
                                $lastest = FundTransaction::where('user_id', $user->id)->latest('id')->first();
                                
                                if ($lastest && $lastest->money_after > 0) {
                                    $money_need_back =  $posConsignment->getTotalMoney() - $posConsignment->getMoneyBack();
                                
                                    if($lastest->money_after > $money_need_back) {
                                        $money_back = $money_need_back;
                                        $posConsignment->isDone = 1;
                                        $posConsignment->save();
                                    } else {
                                        $money_back = $lastest->money_after;
                                    }
                                    PosBack::create([
                                        'name' => 'Auto add posBack Lô:'.$posConsignment->lo,
                                        'money' => $money_back,
                                        'pos_id' => $p->id,
                                        'note' => 'Tự động chia tiền pos',
                                        'lo' => $posConsignment->lo,
                                        'active' => 1,
                                    ]);

                                    $dataCreateTrans['name'] = 'Nạp tiền pos:' . $p->name;
                                    $dataCreateTrans['type'] = 2;
                                    $dataCreateTrans['note'] = 'Auto tiền về pos :'.$p->name.' || lô: '.$posConsignment->lo;
                                    $dataCreateTrans['fund_category_id'] = 3; //
                                    $dataCreateTrans['debitAmount'] = $money_back;
                                    $dataCreateTrans['user_id'] = $user->id;

                                    $fundTransaction =  new FundTransaction();
                                    $fundTransactionService = new FundTransactionService($fundTransaction);
                                    $fundTransactionService->store($dataCreateTrans);

                                } else {
                                    $posConsignment->isDone = 0;
                                    $posConsignment->save();
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $th) {
                DB::rollBack();
            }
            DB::commit();
            }
    }
}
