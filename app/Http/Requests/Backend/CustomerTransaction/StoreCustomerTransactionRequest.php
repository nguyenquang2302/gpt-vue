<?php

namespace App\Http\Requests\Backend\CustomerTransaction;

use App\Models\StoreCustomerTransaction\StoreCustomerTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreCustomerTransactionRequest.
 */
class StoreCustomerTransactionRequest extends FormRequest
{
    /**
     * Determine if the customerCard is authorized to make this request.
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
            'customer_id' => ['required', 'max:100'],
            'key' => ['sometimes', 'max:100'],
            'source' => ['required', 'max:100'],
            'bank_id' => ['required'],
            'bank_code' => ['required'],
            'bank_customer_name' => ['required'],
            'content' => ['required'],
            'money' => ['required'],
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
