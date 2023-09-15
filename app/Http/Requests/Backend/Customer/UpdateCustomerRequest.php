<?php

namespace App\Http\Requests\Backend\Customer;

use App\Models\Customer\Customer;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class UpdateCustomerRequest.
 */
class UpdateCustomerRequest extends FormRequest
{
    /**
     * Determine if the customer is authorized to make this request.
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
            'name' => ['required', 'max:100'],
            'email' => ['sometimes', 'max:255'],
            'active' => ['sometimes', 'boolean'],
            'cmnd' => ['sometimes'],
            'phone' => ['sometimes',Rule::unique('customers')->ignore($this->customer)],
            'address' => ['sometimes'],
            'birth_day' => ['sometimes'],
            'note' => ['sometimes'],
            'province_id' => ['sometimes'],
            'district_id' => ['sometimes'],
            'ward_id' => ['sometimes'],
            'type' => ['sometimes'],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException(__('Only the administrator can update this customer.'));
    }
}
