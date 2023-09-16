<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Backend\Customer\DeleteCustomerRequest;
use App\Http\Requests\Backend\Customer\EditCustomerRequest;
use App\Http\Requests\Backend\Customer\StoreCustomerRequest;
use App\Http\Requests\Backend\Customer\UpdateCustomerRequest;
use App\Models\Customer\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class CustomerController.
 */
class CustomerController
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
    public function index( Request $request)
    {
        $isChecked = $request->input('isChecked'); 
        $rowsPerPage = $request->input('rowsPerPage'); 
        $page = $request->input('page'); 
        $sortBy = $request->get('sortBy','id');
        $sortType = $request->get('sortType','desc');
        $customers = Customer::query();
        if($isChecked === 'true') {
            $customers->where('money','>',0);
        }
        if($isChecked === 'false') {
            $customers->where('money','<',0);

        }
        
        return response([
            'customers' => $customers
                    ->when($request->input('search'), function ($query, $search) {
                        $slug_name =  \Str::slug($search);
                        $query->where('name', 'like', '%' . $search . '%')
                            ->OrWhere('email', 'like', '%' . $search . '%')
                            ->OrWhere('slug', 'like', '%' . $slug_name . '%');
                    })->orderBy($sortBy,$sortType)->paginate($rowsPerPage),
        ], Response::HTTP_OK);
    }

    public function search( Request $request)
    {
        return response([
            'customers' => Customer::query()->select('id', 'name', 'phone')
            ->when($request->input('search'), function ($query, $search) {
                $slug_name =  \Str::slug($search);
                $query->where('name', 'like', '%' . $search . '%')
                    ->OrWhere('phone', 'like', '%' . $search . '%')
                    ->OrWhere('slug', 'like', '%' . $slug_name . '%');

            })->get()
        ], Response::HTTP_OK);
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
        $this->customerService->update($customer, $request->validated());
        return response([
            'msg' => 'Cập nhật tài khoản thành công',
        ], Response::HTTP_OK);
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
}
