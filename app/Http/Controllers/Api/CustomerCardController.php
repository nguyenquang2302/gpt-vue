<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Backend\CustomerCard\DeleteCustomerCardRequest;
use App\Http\Requests\Backend\CustomerCard\EditCustomerCardRequest;
use App\Http\Requests\Backend\CustomerCard\StoreCustomerCardRequest;
use App\Http\Requests\Backend\CustomerCard\UpdateCustomerCardRequest;
use App\Http\Requests\Backend\CustomerCard\UpdateStatementCustomerCardRequest;
use App\Models\Bank\Bank;
use App\Models\Customer\Customer;
use App\Models\CustomerCard\CustomerCard;
use App\Services\CustomerCardService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

/**
 * Class CustomerCardController.
 */
class CustomerCardController
{
    /**
     * @var CustomerCardService
     */
    protected $customerCardService;


    /**
     * CustomerCardController constructor.
     *
     * @param  CustomerCardService  $customerCardService
     */
    public function __construct(CustomerCardService $customerCardService)
    {
        $this->customerCardService = $customerCardService;
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
        $customers = CustomerCard::query()->with('customer');
        if($customerId) {
            $customers->where('customer_id',$customerId);
        }
                   

        return response([
            'customer_cards' =>  $customers ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->OrWhere('end_number', 'like', '%' . $search . '%');
            })->orderBy($sortBy,$sortType)->paginate($rowsPerPage),
        ], Response::HTTP_OK);
    }

    public function search( Request $request)
    {
        return response([
            'customer_cards' => CustomerCard::query()->select('id', 'name')->when($request->input('search'), function ($query, $search) {
                $query->where('customer_id',$search);
            })->get()
        ], Response::HTTP_OK);
    }

    /**
     * @param  StoreCustomerCardRequest  $request
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     * @throws \Throwable
     */
    public function store(StoreCustomerCardRequest $request)
    {
        $customerCard = $this->customerCardService->store($request->validated());

        return response([
            'customerCard' => $customerCard,
            'msg' => 'Đã thêm mới thành công thẻ KHin',
            
        ], Response::HTTP_OK);
    }

    /**
     * @param  CustomerCard  $customerCard
     * @return mixed
     */
    public function show(CustomerCard $customerCard)
    {
        return response([
            'customer_card' => $customerCard,
        ], Response::HTTP_OK);
    }

    /**
     * @param  UpdateCustomerCardRequest  $request
     * @param  CustomerCard  $customerCard
     * @return mixed
     *
     * @throws \Throwable
     */
    public function update(UpdateCustomerCardRequest $request, CustomerCard $customerCard)
    {
        $this->customerCardService->update($customerCard, $request->validated());

        return response([
            'msg' => 'Cập nhật Thẻ thành công',
        ], Response::HTTP_OK);
    }

    /**
     * @param  DeleteCustomerCardRequest  $request
     * @param  CustomerCard  $customerCard
     * @return mixed
     *
     * @throws \App\Exceptions\GeneralException
     */
    public function destroy(DeleteCustomerCardRequest $request, CustomerCard $customerCard)
    {
        if (!Auth::user()->checkRole(['admin'])) {
            return response([
                'customerCard' =>  $customerCard,
                'message' => 'Bạn không có quyền này'
            ], Response::HTTP_FOUND);
        }

        $this->customerCardService->delete($customerCard);

        return response([
            'msg' => 'Đã xoá thành công',
        ], Response::HTTP_OK);
    }

    /**
     * @param  UpdateCustomerCardRequest  $request
     * @param  CustomerCard  $customerCard
     * @return mixed
     *
     * @throws \Throwable
     */
    public function combo(Request $request, CustomerCard $customerCard)
    {
        $now = Carbon::now();
        if ($customerCard->combo_time &&  $customerCard->combo_time > $now) {
            $customerCard->combo_time->addMonths(3);
            $customerCard->save();
        } else {
            $customerCard->combo_time = $now->addMonths(2);
            $customerCard->save();
        }
        return redirect()->route('admin.customerCard.show', $customerCard)->withFlashSuccess(__('Đã đăng kí gói 3 tháng thành công'));
    }

    public function changeSave(Request $request, CustomerCard $customerCard)
    {
        $customerCard->save = !$customerCard->save;
        $customerCard->save();
        if($customerCard->save == 1)
        {
            return redirect()->route('admin.customerCard.index', ['filters[search]'=>$customerCard->end_number])->withFlashSuccess(__('Đã giữ thẻ'));
        }
        return redirect()->route('admin.customerCard.index', ['filters[search]'=>$customerCard->end_number])->withFlashSuccess(__('Đã trả thẻ'));

    }

    public function complate(Request $request, CustomerCard $customerCard)
    {
        $customerCard->date_comlate = Carbon::now();
        $customerCard->save();
        return redirect()->route('admin.customerCard.index', ['filters[search]'=>$customerCard->end_number])->withFlashSuccess(__('Đã xác nhận đáo hạn xong'));

    }

    public function statement(UpdateStatementCustomerCardRequest $request, CustomerCard $customerCard)
    {

        $this->customerCardService->updateStatement($customerCard, $request->validated());
    return redirect()->route('admin.customerCard.index',  ['filters[search]'=>$customerCard->end_number])->withFlashSuccess(__('Đã lưu thông tin sao kê'));

    }


}
