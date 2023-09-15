<?php

namespace App\Http\Requests\Backend\Pos;

use App\Models\Pos\Pos;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdatePosRequest.
 */
class UpdatePosRequest extends FormRequest
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
            'name' => ['required'],
            'telegramChanelId' => ['required'],
            'currency_limit' => ['required'],
            'currency_limit_on_card' => ['required'],
            'currency_limit_on_bill' => ['required'],
            'bill_limit_on_card' => ['required', 'max:31','integer'],
            'fee_bank' => ['required'],
            'user_id_belongto' => ['required'],
            'bank_id' => ['required'],
            'note' => ['sometimes'],
            'active' => ['sometimes', 'in:1'],
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
