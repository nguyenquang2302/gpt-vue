<?php

namespace App\Services;

use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Drawal\Drawal;
use App\Models\FundTransaction\FundTransaction;
use App\Models\Withdrawal\Withdrawal;
use Illuminate\Support\Facades\URL;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService extends BaseService {
    
    public static function  sendMessageCreateDH(Withdrawal $withdrawal) {
        $withdrawalDetails = $withdrawal->withdrawalDetail;
        $text = ' New Đáo Hạn ID: '. $withdrawal->id .' |||| '. 'Số tiền : '. number_format($withdrawal->money_withdrawal);

        if($customerCard = $withdrawal->CustomerCard) {
            $text .=' || Số tài khoản: '. $customerCard->card_number;
        }
        // Telegram::sendMessage([
        //     'chat_id' => env('TELEGRAM_CHANNEL_CREATE_DH_ID', ''),
        //     'parse_mode' => 'HTML',
        //     'text' => $text
        // ]);
        // foreach($withdrawalDetails as $detail) {
        //     $detail_html = 'Số tiền nạp: '. number_format($detail->money).' || Số tiền rút: '. number_format($detail->money_drawal).' || POS: '. $detail->pos->name;;
          
        //     Telegram::sendMessage([
        //         'chat_id' => env('TELEGRAM_CHANNEL_CREATE_DH_ID', ''),
        //         'parse_mode' => 'HTML',
        //         'text' => $detail_html
        //     ]);
        // }
    }

    public static function  sendMessageConfirmDH(Withdrawal $withdrawal) {
        $withdrawalDetails = $withdrawal->withdrawalDetail;
        $text = ' Xác nhận Đáo Hạn ID: '. $withdrawal->id .' |||| '. 'Số tiền : '. number_format($withdrawal->money_withdrawal);

        if($customerCard = $withdrawal->CustomerCard) {
            $text .=' || Số tài khoản: '. $customerCard->card_number;
        }

        // Telegram::sendMessage([
        //     'chat_id' => env('TELEGRAM_CHANNEL_CONFIRM_DH_ID', ''),
        //     'parse_mode' => 'HTML',
        //     'text' => $text
        // ]);

        foreach($withdrawalDetails as $detail) {
            $detail_html = '|| '.'Số tiền nạp: '. number_format($detail->money).' || Số tiền rút: '. number_format($detail->money_drawal).' || POS: '. $detail->pos->name.' || '.$detail->created_at->format('d/m/y h:i');
            if($pos =  $detail->pos) {
                if($telegramChanelId = $pos->telegramChanelId) {
                    // Telegram::sendMessage([
                    //     'chat_id' => $telegramChanelId,
                    //     'parse_mode' => 'HTML',
                    //     'text' => $detail_html
                    // ]);
                }
            }
        }
    }

    public static function  sendMessageCreateRT(Drawal $drawal) {
        $drawalDetails = $drawal->drawalDetail;
        $text = ' Rút Tiền ID: '. $drawal->id .' || || '. 'Số tiền : '. number_format($drawal->money_drawal);
        if($customerCard = $drawal->CustomerCard) {
            $text .=' || Số tài khoản: '. $customerCard->card_number;
        }
        // Telegram::sendMessage([
        //     'chat_id' => env('TELEGRAM_CHANNEL_CREATE_RT_ID', ''),
        //     'parse_mode' => 'HTML',
        //     'text' => $text
        // ]);
        // foreach($drawalDetails as $detail) {
        //     $detail_html = 'Số tiền rút: '. number_format($detail->money).' || POS: '. $detail->pos->name;;
          
        //     Telegram::sendMessage([
        //         'chat_id' => env('TELEGRAM_CHANNEL_CREATE_RT_ID', ''),
        //         'parse_mode' => 'HTML',
        //         'text' => $detail_html
        //     ]);
        // }
    }

    public static function  sendMessageConfirmRT(Drawal $drawal) {

        $drawalDetails = $drawal->drawalDetail;
        $text = ' Xác nhận Rút ID: '. $drawal->id .' || || '. 'Số tiền : '. number_format($drawal->money);

        if($customerCard = $drawal->CustomerCard) {
            $text .=' || Số tài khoản: '. $customerCard->card_number;
        }

        // Telegram::sendMessage([
        //     'chat_id' => env('TELEGRAM_CHANNEL_CONFIRM_RT_ID', ''),
        //     'parse_mode' => 'HTML',
        //     'text' => $text
        // ]);

        foreach($drawalDetails as $detail) {
            $detail_html = '|| '.' Số tiền rút: '. number_format($detail->money).' || POS: '. $detail->pos->name .' || '.$detail->created_at->format('d/m/y h:i');
            if( $pos =  $detail->pos) {
                if($telegramChanelId = $pos->telegramChanelId) {
                    // Telegram::sendMessage([
                    //     'chat_id' => $telegramChanelId,
                    //     'parse_mode' => 'HTML',
                    //     'text' => $detail_html
                    // ]);
                }
            }
            
        }
    }

    public static function  sendMessageCustomerTransaction(CustomerTransaction $customerTransaction) {
        $customer = $customerTransaction->customer;
        $text = 'Thực hiện chuyển tiền cho Khách hàng: '. $customer->name .' ||  || '. 'Số tiền : '. number_format($customerTransaction->money);

        // Telegram::sendMessage([
        //     'chat_id' => env('TELEGRAM_CHANNEL_CUSTOMER_TRANFER_ID', ''),
        //     'parse_mode' => 'HTML',
        //     'text' => $text
        // ]);
    }
    
    public static function  sendMessageFundTransaction(FundTransaction $fundTransaction) {
        $customer = $fundTransaction->fundCategory;
        if($fundTransaction->type == 1 ) {
            $text = "Thêm mới Thu ID: ".$fundTransaction->id." || Debit: ".number_format($fundTransaction->debitAmount)." || Credit: ".number_format($fundTransaction->creditAmount) ." || Before: ".number_format($fundTransaction->money_before)." || Credit: ".number_format($fundTransaction->creditAmount) ." || After: ".number_format($fundTransaction->money_after)." || Nội dung: ". $fundTransaction->note;

        } else {
            $text = "Thêm mới Chi ID: ".$fundTransaction->id." || Debit: ".number_format($fundTransaction->debitAmount)." || Credit: ".number_format($fundTransaction->creditAmount) ." || Before: ".number_format($fundTransaction->money_before)." || Credit: ".number_format($fundTransaction->creditAmount) ." || After: ".number_format($fundTransaction->money_after)." || Nội dung: ". $fundTransaction->note;
            
        }
        // Telegram::sendMessage([
        //     'chat_id' => env('TELEGRAM_CHANNEL_FUND_TRANFER_ID', ''),
        //     'parse_mode' => 'HTML',
        //     'text' => $text
        // ]);
    }
}
