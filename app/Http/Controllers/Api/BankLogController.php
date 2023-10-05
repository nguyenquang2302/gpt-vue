<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Backend\BankLog\DeleteBankLogRequest;
use App\Http\Requests\Backend\BankLog\EditBankLogRequest;
use App\Http\Requests\Backend\BankLog\StoreBankLogRequest;
use App\Http\Requests\Backend\BankLog\UpdateBankLogRequest;
use App\Models\BankLog\BankLog;
use App\Models\FundCategory\FundCategory;
use App\Services\BankLogService;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

/**
 * Class BankLogController.
 */
class BankLogController
{
    /**
     * @var BankLogService
     */
    protected $bankLogService;


    /**
     * BankLogController constructor.
     *
     * @param  BankLogService  $bankLogService
     */
    public function __construct(BankLogService $bankLogService)
    {
        $this->bankLogService = $bankLogService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index( Request $request)
    {
        $isChecked = $request->query('isChecked', null);
        $rowsPerPage = $request->input('rowsPerPage'); 
        $page = $request->input('page'); 
        $sortBy = $request->get('sortBy','transactionDate');
        $sortType = $request->get('sortType','desc');
        $banklogs = BankLog::query()->with('user');
        if($isChecked === 'true') {
            $banklogs->where('isChecked',1);
        }
        if($isChecked === 'false') {
            $banklogs->where('isChecked',0);
        }

        return response([
            'banklogs' =>  $banklogs ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->OrWhere('creditAmount',str_replace(',','',$search ))
                    ->OrWhere('debitAmount',str_replace(',','',$search ));
            })->orderBy($sortBy,$sortType)->paginate($rowsPerPage),
        ], Response::HTTP_OK);
    }

    public function show(BankLog $history)
    {
        return response([
            'banklog' => $history,
        ], Response::HTTP_OK);
        
    }
    /**
     * @param  UpdateBankRequest  $request
     * @param  Bank  $bank
     * @return mixed
     *
     * @throws \Throwable
     */
    public function update(UpdateBankLogRequest $request, BankLog $history)
    {
        $this->bankLogService->update($history, $request->validated());

        return response([
            'msg' => 'Cập nhật Thẻ thành công',
        ], Response::HTTP_OK);

    }
    


}
