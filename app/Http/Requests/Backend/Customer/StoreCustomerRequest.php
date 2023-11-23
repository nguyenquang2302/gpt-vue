<?php

namespace App\Http\Requests\Backend\Customer;

use App\Models\Customer\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreCustomerRequest.
 */
class StoreCustomerRequest extends FormRequest
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
            'active' => ['sometimes', 'in:1'],
            'cmnd' => ['sometimes'],
            'phone' => ['required', Rule::unique('customers'),'regex:/(84|0[3|5|7|8|9])+([0-9]{8})\b/'],
            'address' => ['sometimes'],
            'birth_day' => ['sometimes'],
            'note' => ['sometimes'],
            'province_id' => ['sometimes'],
            'district_id' => ['sometimes'],
            'ward_id' => ['sometimes'],
            'type' => ['sometimes'],
            'fee_customer' => ['sometimes'],
            'status_type' => ['sometimes'],
            'source_type' => ['sometimes'],
            'note' => ['sometimes'],
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
}
