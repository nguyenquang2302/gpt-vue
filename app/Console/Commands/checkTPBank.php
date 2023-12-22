<?php

namespace App\Console\Commands;

use App\Models\Bank\Bank;
use App\Models\BankLog\BankLog;
use App\Models\TPBankLog\TPBankLog;
use Illuminate\Console\Command;

class checkTPBank extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-t-p-bank';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->convertTPtoMB();
    }

    public function convertTPtoMB()
    {
        
        $data = [
                "id" => "12419339268",
                "arrangementId" => "09169173364,VND-1702868486483-92271794a8bba842b37e22f5cce40765dee49c26b66549b18d9294a271e95c58",
                "reference" => "666V00923346AQHM",
                "description" => "Tran Anh Xuan chuyen tien",
                "category" => "transaction_CategoryTransfer",
                "bookingDate" => "2023-12-13",
                "valueDate" => "2023-12-12",
                "amount" => "93124",
                "currency" => "VND",
                "creditDebitIndicator" => "DBIT",
                "runningBalance" => "0",
                "ofsAcctNo" => "7621184239",
                "ofsAcctName" => "TRAN ANH XUAN",
                "creditorBankNameVn" => "Ngân hàng Đầu tư và Phát triển Việt Nam",
                "creditorBankNameEn" => "Bank for Investment and Development of Vietnam"
            
        ];
        $this->excuteData($data,1);

    }

    public function excuteData($data,$user) {
        $tpbanklog = TPBankLog::where('refNo',$data['id'])->first();
        if(!$tpbanklog) {
            $data['refNo'] = $data['id'];
            if($data['creditDebitIndicator'] == 'DBIT'){
                $data['creditAmount'] = 0;
                $data['debitAmount'] = $data['amount'];
            } else {
                $data['creditAmount'] = $data['amount'];
                $data['debitAmount'] = 0;
            }
            $tpbanklog = new TPBankLog($data);
            $tpbanklog->save();
        }
    }
}
