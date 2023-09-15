<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Branch\DeleteBranchRequest;
use App\Http\Requests\Backend\Branch\UpdateBranchRequest;
use App\Models\Branch\Branch;
use App\Services\BranchService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class BranchController extends Controller
{

    /**
     * @var BranchService
     */
    protected $branchService;

    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @var PermissionService
     */
    protected $permissionService;


    /**
     * BranchController constructor.
     *
     * @param  BranchService  $branchService
     * @param  RoleService  $roleService
     * @param  PermissionService  $permissionService
     */
    public function __construct(BranchService $branchService)
    {
        $this->branchService = $branchService;
    }

    /**
     * Display a listing of the resource.
     */
    public function lists(Request $request)
    {

        return response([
            'branchs' => Branch::query()
                    ->when($request->input('search'), function ($query, $search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })->get()
        ], Response::HTTP_OK);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rowsPerPage = $request->input('rowsPerPage'); 
        $page = $request->input('page'); 

        return response([
            'branchs' => Branch::query()
                    ->when($request->input('search'), function ($query, $search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })->paginate($rowsPerPage),
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
     /**
     * @param  Branch  $branch
     * @return mixed
     */
    public function show(Branch $branch)
    {
        return response([
            'branch' => $branch
        ], Response::HTTP_OK);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBranchRequest $request, Branch $branch)

    {
         $this->branchService->update($branch, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteBranchRequest $request, Branch $branch)
    {
       
        $this->branchService->delete($branch);

        return response([
            'msg' => 'Xoá thành công',
        ], Response::HTTP_OK);

    }
}
