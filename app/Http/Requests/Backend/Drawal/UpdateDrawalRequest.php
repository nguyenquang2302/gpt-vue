<?php

namespace App\Http\Requests\Backend\Drawal;

use App\Models\Drawal\Drawal;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateDrawalRequest.
 */
class UpdateDrawalRequest extends FormRequest
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
            'money_drawal' => ['required'],
            'fee_customer' => ['required'],
            'fee_ship' => ['sometimes'],
            'fee_user' => ['sometimes'],
            'fee_money_customer' => ['sometimes'],
            'note' => ['sometimes'],
            'pos_id' => ['sometimes'],
            'money' => ['sometimes'],
            'money_drawal' => ['sometimes'],
            'details' => ['sometimes'],
            'transfer' => ['required', 'in:1,0'],
            'bank_id' => ['required'],
            'bank_customer_name' => ['required'],
            'bank_code' => ['required'],
            'details' => ['sometimes'],
            'datetime'=> ['required'],
            'group_bill' => ['sometimes','array'],
            'group_bill.*'=>['regex:/^[0-9]{0,}\.[0-9]{0,}$/'],
            'stt' => ['sometimes'],

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
        throw new AuthorizationException(__('Only the administrator can update this pos.'));
    }
}
