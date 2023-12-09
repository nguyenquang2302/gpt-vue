<?php

namespace App\Console\Commands;

use App\Models\BankLog\BankLog;
use App\Models\Customer\Customer;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Drawal\Drawal;
use App\Models\FundTransaction\FundTransaction;
use App\Models\Pos\Pos;
use App\Models\PosBack\PosBack;
use App\Models\Users\User;
use App\Models\Withdrawal\Withdrawal;
use App\Models\WithdrawalDetail\WithdrawalDetail;
use App\Services\CustomerTransactionService;
use App\Services\FundTransactionService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\Types\Null_;

class checkMBBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:MBCheck';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    const transaction_url = '127.0.0.1:3000/mb/transaction';
    const username = '0838808080';
    const password = 'Tranng0ctu@';
    const account = '0838808080';
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
        // settings()->set([
        //     'time_check_mb' => Carbon::now()
        // ]);
        $time_check_mb = settings()->get('time_check_mb', 0);
        if ($time_check_mb) {
            $time_check_mb_carbon = Carbon::createFromFormat('Y-m-d H:i:s', $time_check_mb);
        } else {
            $time_check_mb_carbon = Carbon::now();
        }

        $now = Carbon::now();
        $caculator_minutes = $time_check_mb_carbon->diffInMinutes($now);

        if ($caculator_minutes >= 4) {

            settings()->set([
                'time_check_mb' => Carbon::now()
            ]);

            $users = User::where('accountName', '!=', NULL)->get();
            foreach ($users as $user) {
                if ($user->id == 0) {
                } else {

                    $user->last_login_at = Carbon::now();
                    $user->save();
                    $time = [
                        "from" => Carbon::now()->subDays(1)->format('d/m/Y'),
                        "to" => Carbon::now()->format('d/m/Y'),
                    ];
                    $response = Http::post(self::transaction_url, [
                        'user' => $user->accountName,
                        'pass' => $user->passBank,
                        'account' => $user->accountNo,
                        'time' => $time,
                    ]);
                    // if($user->id == 4)
                    // {
                    //     dd($user);
                    // }
                    $lists = ($response->object());
                    if (isset($lists->data)) {
                        foreach ($lists->data as $key => $transaction) {
                            $data = array_reverse((array)$transaction);
                            
                            var_dump($user->name.'-'.$data['refNo'].'-'.$data['debitAmount'].'-'.$data['creditAmount']);

                            $this->excuteData($data, $user);
                        }
                    }
                }
            }
        }
        var_dump('done');
    }

    public function excuteData($data, $user)
    {

        DB::beginTransaction();

        try {
            if ($bankLogs = BankLog::where('refNo', $data['refNo'])->where('creditAmount', $data['creditAmount'])->where('debitAmount', $data['debitAmount'])->first()) {
                // return;
            } else {
                $bankLogs = new BankLog($data);
                $bankLogs->transactionDate = Carbon::createFromFormat('d/m/Y H:i:s', $data['transactionDate']);
                $bankLogs->postingDate = Carbon::createFromFormat('d/m/Y H:i:s', $data['postingDate']);
                $bankLogs->user_id = $user->id;
                $bankLogs->save();
                var_dump($bankLogs->id);
                var_dump($bankLogs['creditAmount'], $bankLogs['debitAmount']);
            }
            if (!$bankLogs->isChecked) {
                // if (!$customer_transaction = CustomerTransaction::where('refNo', $data['refNo'])->first()) {
                if (preg_match('/NTMP\s[0-9]{1,}\s[0-9]{1,}/', $data['description'], $description)) {

                    $bankLogs->content = $description[0];
                    $bankLogs->save();
                    if ($description) {
                        $detail = explode(' ', $description[0]);
                        if ($detail) {

                            if ($detail[1] && $detail[2]) {

                                if ($detail[0] == 'NTMP') {
                                    $pos = Pos::find($detail[1]);

                                    if ($user && $pos) {
                                        //Banklog
                                        if (!$posBack = PosBack::where('refNo', $data['refNo'])->first()) {
                                            $bankLogs->isChecked = true;
                                            $bankLogs->content = $description[0];
                                            $bankLogs->save();

                                            $posBack = new PosBack();
                                            $posBack->name = 'NTMP ' . $detail[1] . ' ' .  $detail[2];
                                            $posBack->money = $data['creditAmount'];
                                            $posBack->pos_id = $detail[1];
                                            $posBack->refNo = $data['refNo'];
                                            $posBack->postingDate =  Carbon::createFromFormat('d/m/Y H:i:s', $data['postingDate']);
                                            $posBack->transactionDate =  Carbon::createFromFormat('d/m/Y H:i:s', $data['transactionDate']);
                                            $posBack->user_id = $user->id;
                                            $posBack->lo = $detail[2];
                                            $posBack->bank_log_id = $bankLogs->id;
                                            $posBack->save();

                                            $data['name'] = 'Nạp tiền máy pos';
                                            $data['type'] = 1;
                                            $data['note'] = $description[0];
                                            $data['fund_category_id'] = 3; //  
                                            $data['creditAmount'] = $data['creditAmount'];
                                            $data['category'] = $data['creditAmount'];
                                            $data['user_id'] = $user->id;
                                            $data['bank_log_id'] = $bankLogs->id;
                                            $data['refNo'] = $data['refNo'];
                                            $fundTransaction =  new FundTransaction;
                                            $fundTransactionService = new FundTransactionService($fundTransaction);
                                            $fundTransactionService->store($data);
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else if (preg_match('/HUY\s[0-9]{1,}/', $data['description'], $description)) {
                    $bankLogs->content = $description[0];
                    $bankLogs->save();
                } else if (preg_match('/NTDH\s[0-9]{1,}\s[0-9]{1,}/', $data['description'], $description)) {
                    if ($description) {
                        $detail = explode(' ', $description[0]);
                        $bankLogs->content = $description[0];
                        $bankLogs->save();

                        if ($detail) {
                            // $customer = Customer::find($detail[2]);
                            // if ($customer && $detail[1] && $detail[2]) {
                            if ($detail[1] && $detail[2]) {
                                if ($detail[0] == 'NTDH') {

                                    $withdrawal = Withdrawal::find($detail[1]);
                                    if ($withdrawal && $withdrawal->isDone) {

                                        if (!$withrawalExits = WithdrawalDetail::where('refNo', $data['refNo'])->first()) {
                                            $customer =  $withdrawal->customer;

                                            if ($withdrawalDetail = WithdrawalDetail::where('withdrawal_id', $withdrawal->id)->where('money', (int)$data['debitAmount'])->first()) {
                                                // banklogs
                                                $bankLogs->isChecked = true;
                                                $bankLogs->content = $description[0];
                                                $bankLogs->save();
                                                $dataCreateTrans['name'] = 'Nạp tiền đáo hạn: ' . $customer->name . ' - ' . $customer->id;
                                                $dataCreateTrans['type'] = 2;
                                                $dataCreateTrans['note'] = $description[0];
                                                $dataCreateTrans['fund_category_id'] = 4; // Nạp tiền đáo hạn
                                                $dataCreateTrans['debitAmount'] = $withdrawalDetail->money;
                                                $dataCreateTrans['user_id'] = $user->id;
                                                $dataCreateTrans['bank_log_id'] = $bankLogs->id;
                                                $dataCreateTrans['refNo'] = $data['refNo'];

                                                $withdrawalDetail->postingDate =  Carbon::createFromFormat('d/m/Y H:i:s', $data['postingDate']);
                                                $withdrawalDetail->transactionDate =  Carbon::createFromFormat('d/m/Y H:i:s', $data['transactionDate']);
                                                $withdrawalDetail->refNo = $data['refNo'];
                                                $withdrawalDetail->isBankChecked = 1;
                                                $withdrawalDetail->bank_log_id = $bankLogs->id;
                                                $withdrawalDetail->save();

                                                $fundTransaction =  new FundTransaction();
                                                $fundTransactionService = new FundTransactionService($fundTransaction);
                                                $fundTransactionService->store($dataCreateTrans);
                                            } else {
                                                $bankLogs->isChecked = false;
                                                $bankLogs->content = $description[0];
                                                $bankLogs->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } else if (preg_match('/CKRT\s[0-9]{1,}\s[0-9]{1,}/', $data['description'], $description)) {
                    $bankLogs->content = $description[0];
                    $bankLogs->save();
                    if ($description) {
                        $detail = explode(' ', $description[0]);

                        if ($detail) {
                            $customer = Customer::find($detail[2]);

                            if ($customer && $detail[1] && $detail[2]) {
                                if ($detail[0] == 'CKRT') {

                                    $customerTransaction = CustomerTransaction::where('source', $detail[0])->where('money', -$data['debitAmount'])->where('customer_id', $detail[2])->where('key', $detail[1])->first();
                                    if (!$customerTransaction) {
                                        if ($data['debitAmount'] > 0) {
                                            $detail = explode(' ', $description[0]);
                                            if ($customer = Customer::find($detail[2])) {
                                                $bankLogs->isChecked = true;
                                                $drawal = Drawal::where('id',$detail[1])->where('isDone',1)->first();

                                                if ($drawal) {
                                                    $bankLogs->content = $description[0];
                                                    $bankLogs->save();
                                                    $customerTransaction = new CustomerTransaction;
                                                    $customerTransactionData = [
                                                        'money' => $data['debitAmount'],
                                                        "customer_id" => $customer->id,
                                                        "key" => $detail[1],
                                                        "source" => 'CKRT',
                                                        "bank_id" => $drawal->bank_id,
                                                        "bank_code" => $data['bankName'],
                                                        "bank_customer_name" => $data['benAccountName'],
                                                        "content" => $description[0],
                                                        "note" => $description[0],
                                                        "postingDate" =>   Carbon::createFromFormat('d/m/Y H:i:s', $data['postingDate']),
                                                        "transactionDate" =>   Carbon::createFromFormat('d/m/Y H:i:s', $data['transactionDate']),
                                                        "refNo" => $data['refNo'],
                                                        "isBankChecked" => 1,
                                                        "bank_log_id" => $bankLogs->id,
                                                    ];
                                                    $customerTransactionService = new CustomerTransactionService($customerTransaction);
                                                    $customerTransactionService->store($customerTransactionData);

                                                    $dataCreateTrans['name'] = 'Chuyển khoản cho user:' . $customer->id;
                                                    $dataCreateTrans['type'] = 2;
                                                    $dataCreateTrans['note'] = $customerTransaction->id;
                                                    $dataCreateTrans['fund_category_id'] = 5; // Customer rút tiền
                                                    $dataCreateTrans['debitAmount'] = $data['debitAmount'];
                                                    $dataCreateTrans['user_id'] = $user->id;
                                                    $dataCreateTrans['bank_log_id'] = $bankLogs->id;
                                                    $dataCreateTrans['refNo'] = $data['refNo'];

                                                    $fundTransaction =  new FundTransaction();
                                                    $fundTransactionService = new FundTransactionService($fundTransaction);
                                                    $fundTransactionService->store($dataCreateTrans);
                                                }
                                            }
                                        }
                                    } else if (!CustomerTransaction::where('refNo', $data['refNo'])->count()) {
                                        $bankLogs->isChecked = false;
                                        $bankLogs->content = $description[0];
                                        $bankLogs->save();
                                    }
                                }
                            }
                        }
                    }
                } else if (preg_match('/CKRTT\s(84|0[3|5|7|8|9])+([0-9]{8})\b/', $data['description'], $description)) {
                    $bankLogs->content = $description[0];
                    $bankLogs->save();
                } else if (preg_match('/NTDHT\s(84|0[3|5|7|8|9])+([0-9]{8})\b/', $data['description'], $description)) {
                    $bankLogs->content = $description[0];
                    $bankLogs->save();
                } else if (preg_match('/TT\s(84|0[3|5|7|8|9])+([0-9]{8})\b/', $data['description'], $description)) {
                    if ($description) {
                        $detail = explode(' ', $description[0]);
                        if ($detail) {
                            if (!CustomerTransaction::where('refNo', $data['refNo'])->count()) {

                                $customer = Customer::where('phone', $detail[1])->first();
                                if ($customer) {
                                    // banklogs
                                    $bankLogs->isChecked = true;
                                    $bankLogs->content = $description[0];
                                    $bankLogs->save();
                                    if ($data['creditAmount'] > 0) {

                                        $money_before = $customer->money;
                                        $money_after = $customer->money + $data['creditAmount'];
                                        $customer_transaction = new CustomerTransaction();
                                        $customer_transaction->postingDate =  Carbon::createFromFormat('d/m/Y H:i:s', $data['postingDate']);
                                        $customer_transaction->transactionDate =  Carbon::createFromFormat('d/m/Y H:i:s', $data['transactionDate']);
                                        $customer_transaction->refNo = $data['refNo'];
                                        $customer_transaction->money = $data['creditAmount'];
                                        $customer_transaction->customer_id = $customer->id;
                                        $customer_transaction->money_before = $money_before;
                                        $customer_transaction->money_after = $money_after;
                                        $customer_transaction->content = $description[0];
                                        $customer_transaction->source = 'Admin';
                                        $customer_transaction->key = Null;
                                        $customer_transaction->isBankChecked = 1;
                                        $customer_transaction->bank_log_id = $bankLogs->id;

                                        $customer_transaction->save();

                                        $customer->money = $money_after;
                                        $customer->save();


                                        $dataCreateTrans['name'] = 'Nạp tiền tài khoản';
                                        $dataCreateTrans['type'] = 1;
                                        $dataCreateTrans['note'] = 'NTTK ' . $description[0];
                                        $dataCreateTrans['fund_category_id'] = 2; // NTTK
                                        $dataCreateTrans['creditAmount'] = $data['creditAmount'];
                                        $dataCreateTrans['category'] = $data['creditAmount'];
                                        $dataCreateTrans['user_id'] = $user->id;
                                        $dataCreateTrans['bank_log_id'] = $bankLogs->id;
                                        $dataCreateTrans['refNo'] = $data['refNo'];

                                        $fundTransaction =  new FundTransaction;
                                        $fundTransactionService = new FundTransactionService($fundTransaction);
                                        $fundTransactionService->store($dataCreateTrans);
                                    } else {
                                        $money_before = $customer->money;
                                        $money_after = $customer->money - $data['debitAmount'];
                                        $customer_transaction = new CustomerTransaction();
                                        $customer_transaction->postingDate =  $bankLogs->postingDate;
                                        $customer_transaction->transactionDate =  $bankLogs->transactionDate;
                                        $customer_transaction->refNo = $data['refNo'];
                                        $customer_transaction->money = -$data['debitAmount'];
                                        $customer_transaction->customer_id = $customer->id;
                                        $customer_transaction->money_before = $money_before;
                                        $customer_transaction->money_after = $money_after;
                                        $customer_transaction->content = $description[0];
                                        $customer_transaction->source = 'Admin';
                                        $customer_transaction->key = Null;
                                        $customer_transaction->isBankChecked = 1;
                                        $customer_transaction->bank_log_id = $bankLogs->id;

                                        $customer_transaction->save();

                                        $customer->money = $money_after;
                                        $customer->save();

                                        $dataCreateTrans['name'] = 'RÚT tiền tài khoản';
                                        $dataCreateTrans['type'] = 2;
                                        $dataCreateTrans['note'] = 'RTTK ' . $description[0];
                                        $dataCreateTrans['fund_category_id'] = 5; // KHRT
                                        $dataCreateTrans['creditAmount'] = $data['creditAmount'];
                                        $dataCreateTrans['debitAmount'] = $data['debitAmount'];
                                        $dataCreateTrans['user_id'] = $user->id;
                                        $dataCreateTrans['bank_log_id'] = $bankLogs->id;
                                        $dataCreateTrans['refNo'] = $data['refNo'];

                                        $fundTransaction =  new FundTransaction;
                                        $fundTransactionService = new FundTransactionService($fundTransaction);
                                        $fundTransactionService->store($dataCreateTrans);
                                    }
                                } else {
                                    // banklogs
                                    $bankLogs->isChecked = false;
                                    $bankLogs->content = $description[0];
                                    $bankLogs->save();
                                }
                            }
                        }
                    }
                } else if (preg_match('/POSUSER\s[A-Z]{1,}/', $data['description'], $description) || preg_match('/POSUSER\s[a-zA-Z0-9]{1,}/', $data['description'], $description1)) {
                    if (!$description && $description1) {
                        $description = $description1;
                    }
                    $detail = explode(' ', $description[0]);
                    $creditAmount = $bankLogs->creditAmount;
                    $debitAmount = $bankLogs->debitAmount;
                    if ($creditAmount > 0) {
                        if ($user1 = User::where('posName',$detail[1])->first()) {
                            $bankLogs->isChecked = true;
                            $bankLogs->content = $description[0];
                            $bankLogs->save();

                            //+ tiền tài khoản MINH để checkout pos
                            $dataCreateTrans['name'] = 'Nạp tiền tài khoản Nội Bộ';
                            $dataCreateTrans['type'] = 1;
                            $dataCreateTrans['note'] = 'Chuyền tiền nội bộ ' . $description[0];
                            $dataCreateTrans['fund_category_id'] = 6; // Chuyển tiền nội bộ
                            $dataCreateTrans['creditAmount'] = $data['creditAmount'];
                            $dataCreateTrans['debitAmount'] = $data['debitAmount'];
                            $dataCreateTrans['user_id'] = $user1->id;
                            $dataCreateTrans['bank_log_id'] = $bankLogs->id;
                            $dataCreateTrans['refNo'] = $data['refNo'];
                            $fundTransaction =  new FundTransaction;
                            $fundTransactionService = new FundTransactionService($fundTransaction);
                            $fundTransactionService->store($dataCreateTrans);


                            $dataCreateTransUserTrans['name'] = 'Chuyển tiền tài khoản Nội Bộ';
                            $dataCreateTransUserTrans['type'] = 1;
                            $dataCreateTransUserTrans['note'] = 'Chuyền tiền nội bộ ' . $description[0];
                            $dataCreateTransUserTrans['fund_category_id'] = 6; // Chuyển tiền nội bộ
                            $dataCreateTransUserTrans['creditAmount'] = $data['creditAmount'];
                            $dataCreateTransUserTrans['debitAmount'] = $data['debitAmount'];
                            $dataCreateTransUserTrans['user_id'] = $bankLogs->user_id;
                            $dataCreateTransUserTrans['bank_log_id'] = $bankLogs->id;
                            $dataCreateTransUserTrans['refNo'] = $data['refNo'];

                            $fundTransaction1 =  new FundTransaction;
                            $fundTransactionService1 = new FundTransactionService($fundTransaction1);
                            $fundTransactionService1->store($dataCreateTransUserTrans);

                        }
                    } elseif ($debitAmount > 0) {
                        // trừ tiền Chủ pos
                        $user = User::where('posName',$detail[1])->first();
                        $userTranfer = User::where('accountNo', $bankLogs->accountNo)->first();
                        if ($user && $userTranfer) {
                            $bankLogs->isChecked = true;
                            $bankLogs->content = $description[0];
                            $bankLogs->save();

                            $dataCreateTrans['name'] = 'Nạp tiền tài khoản Nội Bộ';
                            $dataCreateTrans['type'] = 2;
                            $dataCreateTrans['note'] = 'Chuyền tiền nội bộ ' . $description[0];
                            $dataCreateTrans['fund_category_id'] = 6; // Chuyển tiền nội bộ
                            $dataCreateTrans['creditAmount'] = $data['creditAmount'];
                            $dataCreateTrans['debitAmount'] = $data['debitAmount'];
                            $dataCreateTrans['user_id'] = $user->id;
                            $dataCreateTrans['bank_log_id'] = $bankLogs->id;
                            $dataCreateTrans['refNo'] = $data['refNo'];

                            $fundTransaction =  new FundTransaction;
                            $fundTransactionService = new FundTransactionService($fundTransaction);
                            $fundTransactionService->store($dataCreateTrans);


                            $dataCreateTransUserTrans['name'] = 'Chuyển tiền tài khoản Nội Bộ';
                            $dataCreateTransUserTrans['type'] = 2;
                            $dataCreateTransUserTrans['note'] = 'Chuyền tiền nội bộ ' . $description[0];
                            $dataCreateTransUserTrans['fund_category_id'] = 6; // Chuyển tiền nội bộ
                            $dataCreateTransUserTrans['creditAmount'] = $data['creditAmount'];
                            $dataCreateTransUserTrans['debitAmount'] = $data['debitAmount'];
                            $dataCreateTransUserTrans['user_id'] = $userTranfer->id;
                            $dataCreateTransUserTrans['bank_log_id'] = $bankLogs->id;
                            $dataCreateTransUserTrans['refNo'] = $data['refNo'];

                            $fundTransaction1 =  new FundTransaction;
                            $fundTransactionService1 = new FundTransactionService($fundTransaction1);
                            $fundTransactionService1->store($dataCreateTransUserTrans);
                        }
                        //  trừ tiền người chuyển

                    }
                } else {

                    $creditAmount = $bankLogs->creditAmount;
                    $debitAmount = $bankLogs->debitAmount;
                    if ($debitAmount > 0) {
                        // user nhận tiền
                        if ($bankLogs->benAccountNo) {

                            $user2 = User::where('accountNo', $bankLogs->benAccountNo)->orWhere('benAccountNo', $bankLogs->benAccountNo)->first();
                            if ($user2) {
                                $bankLogs2 =  BankLog::where('user_id', $user2->id)->where('creditAmount', (int)$debitAmount)->where('isChecked', 0)->first();

                                //    dd($bankLogs2);
                                if ($bankLogs2 && $bankLogs->isChecked == 0) {
                                    // chuyển tiền ra
                                    $dataCreateTrans['name'] = 'Chuyển tài khoản Nội Bộ';
                                    $dataCreateTrans['type'] = 2;
                                    $dataCreateTrans['note'] = 'Chuyền tiền nội bộ ' . $user2->id;
                                    $dataCreateTrans['fund_category_id'] = 6; // Chuyển tiền nội bộ
                                    $dataCreateTrans['creditAmount'] = $data['creditAmount'];
                                    $dataCreateTrans['debitAmount'] = $data['debitAmount'];
                                    $dataCreateTrans['user_id'] = $user->id;
                                    $dataCreateTrans['bank_log_id'] = $bankLogs->id;
                                    $dataCreateTrans['refNo'] = $data['refNo'];
                                    $fundTransaction =  new FundTransaction;
                                    $fundTransactionService = new FundTransactionService($fundTransaction);
                                    $fundTransactionService->store($dataCreateTrans);
                                    // nhận tiền vào

                                    $dataCreateTrans2['name'] = 'Chuyển tài khoản Nội Bộ';
                                    $dataCreateTrans2['type'] = 1;
                                    $dataCreateTrans2['note'] = 'Chuyền tiền nội bộ ' . $user->id;
                                    $dataCreateTrans2['fund_category_id'] = 6; // Chuyển tiền nội bộ
                                    $dataCreateTrans2['creditAmount'] = $bankLogs2['creditAmount'];
                                    $dataCreateTrans2['debitAmount'] = $bankLogs2['debitAmount'];
                                    $dataCreateTrans2['user_id'] = $user2->id;
                                    $dataCreateTrans2['bank_log_id'] = $bankLogs->id;
                                    $dataCreateTrans2['refNo'] = $bankLogs2['refNo'];
                                    $fundTransaction2 =  new FundTransaction;
                                    $fundTransactionService2 = new FundTransactionService($fundTransaction2);
                                    $fundTransactionService2->store($dataCreateTrans2);
                                    $bankLogs2->isChecked = true;
                                    $bankLogs2->content = 'CTNB' .  $user->id;
                                    $bankLogs2->save();

                                    $bankLogs->isChecked = true;
                                    $bankLogs->content = 'CTNB' .  $user2->id;
                                    $bankLogs->save();
                                }
                            }
                        }
                    } else {
                        $bankLogs->save();
                    }
                }
            }
            DB::commit();
        } catch (\Throwable $th) {
            var_dump($th);
            DB::rollBack();
            return;
        }
    }
}
