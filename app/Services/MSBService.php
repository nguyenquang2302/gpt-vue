<?php
namespace App\Services;

use GuzzleHttp\Client;

class MSBService {
    private $tokenNo = null,
    $accountNo = null,
    $user,
    $pass,
    $uiid = '13c49848ee81f110';
    private  $URL = 'https://ebank.msb.com.vn/IBSRetail';
    public function __construct($user, $pass) {
        $this->user = $user;
        $this->pass = $pass;
    }

    public function setUUID($uiid) {
        $this->uiid = $uiid;
    }

    public function setTokenNo($tokenNo = "") {
        $tokenNo = file_get_contents(__DIR__ . '\tokenNo');
        $this->tokenNo = $tokenNo;
    }

    public function setAccountNo($accountNo = '') {
        $accountNo = file_get_contents(__DIR__ . '\accountNo');
        $this->accountNo = $accountNo;
    }

    public function sendSms() {
        $sData = $this->requestSv('/register/sendSmsActive.do', [
            "userName" => $this->user,
            "password" => $this->pass,
            "uiid" => $this->uiid,
            "version" => '5.1.9',
            "lang" => 'vi_VN',
        ]);
        file_put_contents(__DIR__ . '\tranId', $sData['data']['tranId']);
        // echo json_encode($sData);
        return $sData;

    }

    public function verifyDevice($otp) {
        $tranId = file_get_contents(__DIR__ . '\tranId');
        $sData = $this->requestSv('/register/activeService.do', [
            'otpInput' => $otp,
            'tranId' => $tranId,
            'uiid' => $this->uiid,
            'playID' => 'a3e5983a-f339-4645-801a-b9502b74bdae',
            'fireBaseToken' => '',
            'lang' => 'vi_VN',
        ]);
        // $this->activeCode = $sData['data']['activeCode'];
        file_put_contents(__DIR__ . '\activeCode', $sData['data']['activeCode']);
        // echo json_encode($sData);
        return $sData;
    }

    public function login() {
        $activeCode = file_get_contents(__DIR__ . '\activeCode');

        $sData = $this->requestSv('/login/authByPassword.do', [
            "userNameOrMobile" => $this->user,
            "password" => $this->pass,
            "activeCode" => $activeCode,
            "uiid" => $this->uiid,
            "version" => '5.2.4',
            "prepaid" => true,
            "lang" => 'vi_VN',
            // "tokenNo" => $token,
        ]);
        if ($sData['status'] == 500) {
            return;
        }

        $this->tokenNo = $sData['data']['tokenNo'];

        file_put_contents(__DIR__ . '\tokenNo', $sData['data']['tokenNo']);
        return $sData;
    }

    public function configDevice($otp) {
        $this->verifyDevice($otp);
        $sData = $this->login();
        $res = $this->getListWallet();

        file_put_contents(__DIR__ . '\accountNo', $res['data'][0]['acctNo']);
        return $sData;
    }

    public function getListWallet() {

        $sData = $this->requestSv('/account/list.do', [
            "acctType" => "'CA','LN','SA','PPC'",
            "status" => "'ACTV','DORM','MATU'",
            "tokenNo" => $this->tokenNo,
            "lang" => 'vi_VN',
        ]);

        // echo json_encode($sData);
        return $sData;
    }

    public function getTransaction($time) {
        //yyyy-mm-dd
        $sData = $this->requestSv('/history/byAccount.do', [
            "fromDate" => $time['from'],
            "toDate" => $time['to'],
            "fromAmount" => 0,
            "toAmount" => "999999999999",
            "acctNo" => $this->accountNo,
            "page" => 1,
            "queryType" => -1,
            "tokenNo" => $this->tokenNo,
            "lang" => 'vi_VN',
        ]);
        // echo json_encode($sData);
        return $sData;
    }

    public function checkAccount($account, $bankCode) {
        $sData = $this->requestSv('/account/getBenefitSML.do', [
            "bankCode" => $bankCode,
            "beneficiaryAccount" => $account,
            "type" => "acctType",
            "fromAcct" => $this->accountNo,
            "queryType" => -1,
            "tokenNo" => $this->tokenNo,
            "lang" => 'vi_VN',
        ]);
        $res = [
            'account' => $account,
            'bankId' => $bankCode,
            'name' => $sData['data']['beneficiaryName'],
            'bankName' => $sData['data']['beneficiaryBankName'],
        ];
        // echo json_encode($res);
        return $res;
    }

    public function configTransfer($info, $amount, $content) {
        $date = date("Y-m-d");
        $sData = $this->requestSv('/transfer/sendOTP.do', [
            'type' => 'A',
            'fromAcc' => $this->accountNo,
            'amount' => $amount,
            'purpose' => $content,
            'toBenefitAcc' => $info['account'],
            'toBenefitName' => $info['name'],
            'toBenefitBranchId' => '',
            'toBenefitBankId' => $info['bankId'],
            'toBenefitBankName' => $info['bankName'],
            'toBenefitBranchName' => '',
            'isNow' => true,
            'transDate' => $date,
            'validatedDate' => $date,
            'expiredDate' => $date,
            'frqType' => 'D',
            'endType' => 'L',
            'frqLimit' => 1,
            'debitDate' => '',
            'notSendOTP' => true,
            'cs' => '',
            "tokenNo" => $this->tokenNo,
            "lang" => 'vi_VN',
        ]);

        // echo json_encode($sData);
        return $sData['data']['tokenTransaction'];
    }

    public function confirmTransfer($tokenTrans) {
        $sData = $this->requestSv('/common/sendOTPOnly.do', [
            "tokenTransaction" => $tokenTrans,
            "tokenNo" => $this->tokenNo,
            "lang" => 'vi_VN',
        ]);

        $res = [
            'tokenTransaction' => $sData['data']['tokenTransaction'],
            'sessionId' => $sData['data']['sessionId'],
        ];

        return $res;
    }

    public function verifyTransferOTP($token, $otp) {
        $sData = $this->requestSv('/transfer/compleleSML.do', [
            "tokenTransaction" => $token['tokenTransaction'],
            "sessionId" => $token['sessionId'],
            "otpInput" => $otp,
            "cs" => '',
            "tokenNo" => $this->tokenNo,
            "lang" => 'vi_VN',
        ]);
        // echo json_encode($sData);
        return $sData;
    }

    public function checkBill($contactNo, $service) {

        $sData = $this->requestSv('/billpayment/checkBill.do', [
            "contactNo" => $contactNo,
            "serviceId" => $service,
            "tokenNo" => $this->tokenNo,
            "lang" => 'vi_VN',
        ]);

        return $sData;
    }

    public function listService() {

        return LIST_SERVICE;

    }

    private function requestSv($path, $body) {
        $client = new Client();
        $response = $client->request('POS', $this->URL.$path, ['query' => $body]);
        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        dd($statusCode,$content);
        // $curl->_setURL(URL . $path);
        // echo json_encode($body);
        // $curl->_setPostData($body);

        // $curl->_setUserAgent('okhttp/3.12.12');
        // $curl->_run();

        // $sData = json_decode($curl->_getData(), true);
        return $sData;
    }
}
