<?php

namespace App\Http\Controllers\Api;

use App\Domains\Auth\Models\User;
use App\Http\Requests\Backend\Withdrawal\DeleteWithdrawalRequest;
use App\Http\Requests\Backend\Withdrawal\EditWithdrawalRequest;
use App\Http\Requests\Backend\Withdrawal\StoreWithdrawalRequest;
use App\Http\Requests\Backend\Withdrawal\UpdateWithdrawalRequest;
use App\Models\CustomerCard\CustomerCard;
use App\Models\Pos\Pos;
use App\Models\Withdrawal\Withdrawal;
use App\Services\WithdrawalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class WithdrawalController.
 */
class WithdrawalController
{
    /**
     * @var WithdrawalService
     */
    protected $withdrawalService;


    /**
     * WithdrawalController constructor.
     *
     * @param  WithdrawalService  $withdrawalService
     */
    public function __construct(WithdrawalService $withdrawalService)
    {
        $this->withdrawalService = $withdrawalService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        
        $customerId= $request->query('customer_id', null);
        $rowsPerPage = $request->input('rowsPerPage'); 
        $page = $request->input('page'); 
        $sortBy = $request->get('sortBy','id');
        $sortType = $request->get('sortType','desc');
        $withdrawals = Withdrawal::query()->with('customer');
        $user = auth()->user();
        if($typeDate = $request->get('typeData')) 
        {
            if((int)$typeDate === 1) {
                $withdrawals->where('user_id',$user->id);
            }
            if((int)$typeDate === 2) {
                $withdrawals->where('branch_id',$user->branch_id);
            }
            

        }
        if($customerId) {
            $withdrawals->where('customer_id',$customerId);
        }

        return response([
            'withdrawals' =>  $withdrawals->with('user')->with('userBelongto')->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->orderBy($sortBy,$sortType)->paginate($rowsPerPage),
        ], Response::HTTP_OK);
    }


    /**
     * @param  StoreWithdrawalRequest  $request
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     */
    public function store(StoreWithdrawalRequest $request)
    {
        $withdrawal = $this->withdrawalService->store($request->validated());
        return response([
            'withdrawal' =>  $withdrawal,
            'message' => 'Thêm mới thành công'
        ], Response::HTTP_OK);
    }

    /**
     * @param  Withdrawal  $withdrawal
     * @return mixed
     */
    public function show(Withdrawal $withdrawal)
    {
        $withdrawal->details = $withdrawal->withdrawalDetail;
        $withdrawal->customerCard = $withdrawal->customerCard;
        return response([
            'withdrawal' => $withdrawal
        ], Response::HTTP_OK);
    }


    /**
     * @param  UpdateWithdrawalRequest  $request
     * @param  Withdrawal  $withdrawal
     * @return mixed
     *
     * @throws \Throwable
     */
    public function update(UpdateWithdrawalRequest $request, Withdrawal $withdrawal)
    {
        if($withdrawal->isDone) {

            return response([
                'withdrawal' =>  $withdrawal,
                'message' => ' Giao dịch đã xác nhận không thể  chỉnh sửa'
            ], Response::HTTP_FOUND);
        }
        
        $this->withdrawalService->update($withdrawal, $request->validated());

        return response([
            'withdrawal' =>  $withdrawal,
            'message' => 'Cập nhật thành công'
        ], Response::HTTP_OK);
    }

    /**
     * @param  DeleteWithdrawalRequest  $request
     * @param  Withdrawal  $withdrawal
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function destroy(DeleteWithdrawalRequest $request, Withdrawal $withdrawal)
    {
        if (!Auth::user()->checkRole(['admin'])) {
            return response([
                'withdrawal' =>  $withdrawal,
                'message' => 'Bạn không có quyền này'
            ], Response::HTTP_FOUND);
        }
        $this->withdrawalService->delete($withdrawal);
        return response([
            'withdrawal' =>  $withdrawal,
            'message' => 'Xoá thành công'
        ], Response::HTTP_OK);
    }

    /**
     * @param  DeleteDrawalRequest  $request
     * @param  Drawal  $drawal
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function reDone(Withdrawal $withdrawal)
    {
        if (!Auth::user()->checkRole(['admin'])) {
            return response([
                'withdrawal' =>  $withdrawal,
                'message' => 'Bạn không có quyền này'
            ], Response::HTTP_FOUND);
        }
        $this->withdrawalService->reDone($withdrawal);
        return response([
            'withdrawal' =>  $withdrawal,
            'message' => 'Cập nhật thành công'
        ], Response::HTTP_OK);
    }

    
    public function verify(Withdrawal $withdrawal)
    {
        if($withdrawal->isDone) {

            return response([
                'withdrawal' =>  $withdrawal,
                'message' => ' Giao dịch đã xác nhận không thể  chỉnh sửa'
            ], Response::HTTP_FOUND);
        }

        $this->withdrawalService->verify($withdrawal, true);
        
        return response([
            'withdrawal' =>  $withdrawal,
            'message' => 'Cập nhật thành công'
        ], Response::HTTP_OK);
    }
}
