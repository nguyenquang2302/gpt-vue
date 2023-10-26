<?php

namespace App\Http\Requests\Backend\Withdrawal;

use App\Models\Withdrawal\Withdrawal;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreWithdrawalRequest.
 */
class StoreWithdrawalRequest extends FormRequest
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
            'user_fee_id' => ['sometimes'],
            'money_withdrawal' => ['required'],
            'fee_customer' => ['required', ],
            'fee_ship' => ['sometimes'],
            'fee_user' => ['sometimes'],
            'fee_money_customer' => ['sometimes'],
            'note' => ['sometimes'],
            'pos_id' => ['sometimes'],
            'money' => ['sometimes'],
            'money_drawal' => ['sometimes'],
            'customer_card_id' => ['required'],
            'datetime' => ['sometimes'],
            'isOwnerPos' => ['sometimes'],
            'details' => ['sometimes'],
            'group_bill' => ['sometimes'],
            'group_bill' => ['sometimes','array'],
            'group_bill.*'=>['nullable', 'sometimes', 'regex:/^[0-9]{0,}\.[0-9]{0,}$/'],
            'stt' => ['sometimes'],
            'addFee' => ['sometimes'],

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
