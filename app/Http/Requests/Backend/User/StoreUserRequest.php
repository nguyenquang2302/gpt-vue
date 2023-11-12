<?php

namespace App\Http\Requests\Backend\User;

use App\Models\Users\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use LangleyFoxall\LaravelNISTPasswordRules\PasswordRules;

/**
 * Class StoreUserRequest.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'type' => ['required', Rule::in([User::TYPE_ADMIN, User::TYPE_USER,User::TYPE_MANAGER,User::TYPE_MOD,User::TYPE_STAFF,User::TYPE_BANK,User::TYPE_POS,User::TYPE_MANAGER_VIP_2,User::TYPE_MANAGER_VIP,User::TYPE_PARTNER,User::TYPE_TELE_SALES])],
            'name' => ['required', 'max:100'],
            'email' => ['required', 'max:255', 'email', Rule::unique('users')],
            'password' => ['max:100'],
            'active' => ['sometimes', 'in:1'],
            'email_verified' => ['sometimes', 'in:1'],
            'send_confirmation_email' => ['sometimes', 'in:1'],
            'roles' => ['sometimes', 'array'],
            'roles.*' => [Rule::exists('roles', 'id')->where('type', $this->type)],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => [Rule::exists('permissions', 'id')->where('type', $this->type)],
            'accountName' => ['sometimes'],
            'benAccountNo' => ['sometimes'],
            'accountNo' => ['sometimes'],
            'activeBank' => ['sometimes'],
            'passBank' => ['sometimes'],
            'autoPosBack' => ['sometimes'],
            'branch_id' => ['sometimes'],
            'posName' => ['sometimes'],
            'time_partner' => ['sometimes'],
            'fee_partner' => ['sometimes'],
            
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'roles.*.exists' => __('One or more roles were not found or are not allowed to be associated with this user type.'),
            'permissions.*.exists' => __('One or more permissions were not found or are not allowed to be associated with this user type.'),
        ];
    }
}
