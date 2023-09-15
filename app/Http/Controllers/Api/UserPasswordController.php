<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Backend\User\UpdateUserPasswordRequest;
use App\Models\Users\User;
use App\Services\UserService;
use Illuminate\Http\Response;

/**
 * Class UserPasswordController.
 */
class UserPasswordController
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * UserPasswordController constructor.
     *
     * @param  UserService  $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }


    /**
     * @param  UpdateUserPasswordRequest  $request
     * @param  User  $user
     * @return mixed
     *
     * @throws \Throwable
     */
    public function update(UpdateUserPasswordRequest $request, User $user)
    {
        $this->userService->updatePassword($user, $request->validated());
        return response([
            'msg' => 'Cập nhật tài khoản thành công',
        ], Response::HTTP_OK);

    }
}
