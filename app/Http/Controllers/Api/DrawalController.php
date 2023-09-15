<?php

namespace App\Http\Controllers\Api;

use App\Domains\Auth\Models\User;
use App\Http\Requests\Backend\Drawal\DeleteDrawalRequest;
use App\Http\Requests\Backend\Drawal\EditDrawalRequest;
use App\Http\Requests\Backend\Drawal\StoreDrawalRequest;
use App\Http\Requests\Backend\Drawal\UpdateDrawalRequest;
use App\Models\Bank\Bank;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Models\CustomerTransaction\CustomerTransaction;
use App\Models\Pos\Pos;
use App\Models\Drawal\Drawal;
use App\Services\DrawalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class DrawalController.
 */
class DrawalController
{
    /**
     * @var DrawalService
     */
    protected $drawalService;


    /**
     * DrawalController constructor.
     *
     * @param  DrawalService  $drawalService
     */
    public function __construct(DrawalService $drawalService)
    {
        $this->drawalService = $drawalService;
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
        $customers = Drawal::query()->with('customer');
        if($customerId) {
            $customers->where('customer_id',$customerId);
        }

        return response([
            'drawals' =>  $customers ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%');
            })->orderBy($sortBy,$sortType)->paginate($rowsPerPage),
        ], Response::HTTP_OK);
        
    }

    /**
     * @param  StoreDrawalRequest  $request
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     */
    public function store(StoreDrawalRequest $request)
    {
        
        $drawal = $this->drawalService->store($request->validated());

        return response([
            'drawal' =>  $drawal,
            'message' => 'Thêm mới thành công'
        ], Response::HTTP_OK);
    }

    /**
     * @param  Drawal  $drawal
     * @return mixed
     */
    public function show(Drawal $drawal)
    {
        $drawal->details = $drawal->drawalDetail;
        return response([
            'drawal' => $drawal
        ], Response::HTTP_OK);
        
            
    }
    /**
     * @param  UpdateDrawalRequest  $request
     * @param  Drawal  $drawal
     * @return mixed
     *
     * @throws \Throwable
     */
    public function update(UpdateDrawalRequest $request, Drawal $drawal)
    {

        if($drawal->isDone) {

            return response([
                'drawal' =>  $drawal,
                'message' => ' Giao dịch đã xác nhận không thể  chỉnh sửa'
            ], Response::HTTP_FOUND);
        }
        
        $this->drawalService->update($drawal, $request->validated());


        return response([
            'drawal' =>  $drawal,
            'message' => 'Cập nhật thành công'
        ], Response::HTTP_OK);
    }

    /**
     * @param  DeleteDrawalRequest  $request
     * @param  Drawal  $drawal
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function destroy(DeleteDrawalRequest $request, Drawal $drawal)
    {
        if (!Auth::user()->checkRole(['admin'])) {
            return response([
                'drawal' =>  $drawal,
                'message' => 'Bạn không có quyền này'
            ], Response::HTTP_FOUND);
        }
        $this->drawalService->delete($drawal);

        return redirect()->route('admin.drawal.deleted')->withFlashSuccess(__('The drawal was successfully deleted.'));
    }

    /**
     * @param  DeleteDrawalRequest  $request
     * @param  Drawal  $drawal
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function reDone(Drawal $drawal)
    {

        if (!Auth::user()->checkRole(['admin'])) {
            return response([
                'drawal' =>  $drawal,
                'message' => 'Bạn không có quyền này'
            ], Response::HTTP_FOUND);
        }
        $this->drawalService->reDone($drawal);
        return response([
            'drawal' =>  $drawal,
            'message' => 'Cập nhật thành công'
        ], Response::HTTP_OK);
    }

    
    public function verify(Drawal $drawal)
    {
        if($drawal->isDone) {

            return response([
                'drawal' =>  $drawal,
                'message' => ' Giao dịch đã xác nhận không thể  chỉnh sửa'
            ], Response::HTTP_FOUND);
        }
        $this->drawalService->verify($drawal, true);
        
        return response([
            'drawal' =>  $drawal,
            'message' => 'Cập nhật thành công'
        ], Response::HTTP_OK);
    }

}