<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Backend\Customer\DeleteCustomerRequest;
use App\Http\Requests\Backend\Customer\EditCustomerRequest;
use App\Http\Requests\Backend\Customer\StoreCustomerRequest;
use App\Http\Requests\Backend\Customer\UpdateCustomerRequest;
use App\Models\Branch\Branch;
use App\Models\Customer\Customer;
use App\Models\CustomerSchedule\CustomerSchedule;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class CustomerController.
 */
class StaffCustomerController
{
    /**
     * @var CustomerService
     */
    protected $customerService;


    /**
     * CustomerController constructor.
     *
     * @param  CustomerService  $customerService
     */
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {
        $isChecked = $request->input('isChecked');
        $rowsPerPage = $request->input('rowsPerPage');
        $sortBy = $request->get('sortBy', 'id');
        $sortType = $request->get('sortType', 'desc');
        $customers = Customer::query();
        $customers->with(['schedule' => function ($query) { 
            $query->orderby('id','desc')->first();
        }]);
        if ($isChecked === 'true') {
            $customers->where('money', '>', 0);
        }
        if ($isChecked === 'false') {
            $customers->where('money', '<', 0);
        }
        if ($isChecked === 'invest') {
            $customers->where('type', 2);
            $customers->where('user_id', auth()->user()->id);
            return response([
                'customers' => $customers->select('id', 'name', 'phone', 'CMND')->with('user')
                    ->when($request->input('search'), function ($query, $search) {
                        $slug_name =  \Str::slug($search);
                        $query->where('name', 'like', '%' . $search . '%')
                            ->OrWhere('email', 'like', '%' . $search . '%')
                            ->OrWhere('slug', 'like', '%' . $slug_name . '%');
                    })->orderBy($sortBy, $sortType)->paginate($rowsPerPage),
            ], Response::HTTP_OK);
        }
        if (auth()->user()->checkRole(['partner'])) {
            $customers->where('user_id', auth()->user()->id);
            return response([
                'customers' => $customers->select('id', 'name', 'phone', 'CMND')->with('user')
                    ->when($request->input('search'), function ($query, $search) {
                        $slug_name =  \Str::slug($search);
                        $query->where('name', 'like', '%' . $search . '%')
                            ->OrWhere('email', 'like', '%' . $search . '%')
                            ->OrWhere('slug', 'like', '%' . $slug_name . '%');
                    })->orderBy($sortBy, $sortType)->paginate($rowsPerPage),
            ], Response::HTTP_OK);
        } else {

            if (!$request->input('search')) {
                $customers->where('user_id', auth()->user()->id);
            }
            return response([
                'customers' => $customers->with('user')
                    ->when($request->input('search'), function ($query, $search) {
                        $slug_name =  \Str::slug($search);
                        $query->where('phone', $slug_name);
                    })->orderBy($sortBy, $sortType)->paginate($rowsPerPage),
            ], Response::HTTP_OK);
        }
    }

    public function search(Request $request)
    {
        if (auth()->user()->checkRole(['partner'])) {
            return response([
                'customers' => Customer::query()->select('id', 'name', 'phone')->where('user_id', auth()->user()->id)
                    ->when($request->input('query'), function ($query, $search) {
                        $slug_name =  \Str::slug($search);
                        $query->where('name', 'like', '%' . $search . '%')
                            ->OrWhere('phone', 'like', '%' . $search . '%')
                            ->OrWhere('slug', 'like', '%' . $slug_name . '%');
                    })->get()
            ], Response::HTTP_OK);
        } else {
            return response([
                'customers' => Customer::query()->select('id', 'name', 'phone')
                    ->when($request->input('query'), function ($query, $search) {
                        $slug_name =  \Str::slug($search);
                        $query->where('name', 'like', '%' . $search . '%')
                            ->OrWhere('phone', 'like', '%' . $search . '%')
                            ->OrWhere('slug', 'like', '%' . $slug_name . '%');
                    })->get()
            ], Response::HTTP_OK);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $customer = $this->customerService->store($request->validated());
        return response([
            'customer' => $customer,
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
    /**
     * @param  Customer  $customer
     * @return mixed
     */
    public function show(Customer $customer)
    {
        return response([
            'customer' => $customer,
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        if ($customer->user_id == Auth::user()->id) {
            $this->customerService->update($customer, $request->validated());
            return response([
                'msg' => 'Cập nhật tài khoản thành công',
            ], Response::HTTP_OK);
        }
        return response([
            'msg' => 'Không có quyền cập nhật',
        ], Response::HTTP_FORBIDDEN);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteCustomerRequest $request, Customer $customer)
    {
        if (!Auth::user()->checkRole(['admin'])) {
            return response([
                'customer' =>  $customer,
                'message' => 'Bạn không có quyền này'
            ], Response::HTTP_FOUND);
        }

        $this->customerService->delete($customer);

        return response([
            'msg' => 'Xoá thành công',
        ], Response::HTTP_OK);
    }

    public function schedule(Request $request)
    {
        try {
            $CustomerSchedule = new CustomerSchedule();
            $CustomerSchedule->branch_id = $request->get('branch_id');
            $CustomerSchedule->customer_id = $request->get('customer_id');
            $CustomerSchedule->name = $request->get('name');
            $CustomerSchedule->note = Branch::find($request->get('branch_id'))->name . '_' . $request->get('schedule') . '_' . $request->get('note');
            $CustomerSchedule->schedule = $request->get('schedule');
            $CustomerSchedule->save();
        } catch (\Throwable $th) {
            return response([
                'message' => 'Vui lòng nhập đúng thông tin'
            ], Response::HTTP_FOUND);
        }
    }
}
