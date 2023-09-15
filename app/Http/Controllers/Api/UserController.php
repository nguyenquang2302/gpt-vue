<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\User\DeleteUserRequest;
use App\Http\Requests\Backend\User\StoreUserRequest;
use App\Http\Requests\Backend\User\UpdateUserRequest;
use App\Models\Users\User;
use App\Services\PermissionService;
use App\Services\RoleService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class UserController extends Controller
{

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var RoleService
     */
    protected $roleService;

    /**
     * @var PermissionService
     */
    protected $permissionService;


    /**
     * UserController constructor.
     *
     * @param  UserService  $userService
     * @param  RoleService  $roleService
     * @param  PermissionService  $permissionService
     */
    public function __construct(UserService $userService, RoleService $roleService, PermissionService $permissionService)
    {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rowsPerPage = $request->input('rowsPerPage'); 
        $page = $request->input('page'); 

        return response([
            'users' => User::query()
                    ->when($request->input('search'), function ($query, $search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->OrWhere('email', 'like', '%' . $search . '%');
                    })->paginate($rowsPerPage),
        ], Response::HTTP_OK);
    }

    public function search( Request $request)
    {
        return response([
            'users' => User::query()->select('id', 'name', 'phone')
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->OrWhere('phone', 'like', '%' . $search . '%');
            })->get()
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->store($request->validated());
        return response([
            'user' => $user,
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     */
     /**
     * @param  User  $user
     * @return mixed
     */
    public function show(User $user)
    {
        return response([
            'user' => $user,
        ], Response::HTTP_OK);
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated());
        return response([
            'msg' => 'Cập nhật tài khoản thành công',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteUserRequest $request, User $user)
    {
       
        if (!Auth::user()->checkRole(['admin'])) {
            return response([
                'user' =>  $user,
                'message' => 'Bạn không có quyền này'
            ], Response::HTTP_FOUND);
        }

        $this->userService->delete($user);

        return response([
            'msg' => 'Xoá thành công',
        ], Response::HTTP_OK);

    }
}
