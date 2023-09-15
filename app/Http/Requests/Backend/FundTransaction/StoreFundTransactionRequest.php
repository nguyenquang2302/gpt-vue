<?php

namespace App\Http\Requests\Backend\FundTransaction;

use App\Models\Drawal\Drawal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreDrawalRequest.
 */
class StoreFundTransactionRequest extends FormRequest
{
    /**
     * Determine if the pos is authorized to make this request.
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
            'name' => ['sometimes'],
            'fund_category_id' => ['required'],
            'type' => ['required'],
            'note' => ['sometimes'],
            'debitAmount' => ['sometimes'],
            'creditAmount' => ['sometimes'],
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
